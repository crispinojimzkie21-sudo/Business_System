<?php
/**
 * Test HTTP Login Flow - Fixed Version
 * Tests the actual login via HTTP request with proper session handling
 */

$baseUrl = 'http://127.0.0.1:8000';
$loginUrl = $baseUrl . '/login';
$dashboardUrl = $baseUrl . '/dashboard';
$cookieFile = __DIR__ . '/test_cookies.txt';

echo "=== Testing HTTP Login Flow ===\n\n";

// Clean up old cookie file
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

// Step 1: Get the login page and extract CSRF token
echo "Step 1: Fetching login page to get CSRF token...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

if ($curlError) {
    echo "❌ cURL Error: $curlError\n";
    echo "   Make sure the Laravel server is running on $baseUrl\n";
    exit(1);
}

if ($httpCode !== 200) {
    echo "❌ Failed to fetch login page. HTTP Code: $httpCode\n";
    exit(1);
}

echo "✅ Login page fetched successfully (HTTP $httpCode)\n";

// Extract CSRF token from the response - try multiple patterns
$csrfToken = null;

// Try to find _token in form input
if (preg_match('/name="_token"\s+[^>]*value="([^"]+)"/', $response, $matches)) {
    $csrfToken = $matches[1];
    echo "   CSRF Token (form _token): " . substr($csrfToken, 0, 20) . "...\n";
} elseif (preg_match('/<input[^>]*name="_token"[^>]*value="([^"]+)"/', $response, $matches)) {
    $csrfToken = $matches[1];
    echo "   CSRF Token (form _token alt): " . substr($csrfToken, 0, 20) . "...\n";
}

// Also try to get XSRF-TOKEN cookie value
$xcsrfToken = null;
if (preg_match('/XSRF-TOKEN[^;]*;\s*([^;]+)/i', $response, $matches)) {
    // This is the raw response, we need to check cookies separately
}

curl_close($ch);

// Check what cookies were set
echo "\n   Cookies received:\n";
if (file_exists($cookieFile)) {
    $cookies = file_get_contents($cookieFile);
    echo "   " . str_replace("\n", "\n   ", $cookies) . "\n";
}

if (!$csrfToken) {
    echo "❌ Could not find CSRF token in the response!\n";
    // Show a snippet of the response for debugging
    echo "   Response snippet: " . substr($response, 0, 1000) . "\n";
    exit(1);
}

// Step 2: Submit login form - Make sure to include all cookies
echo "\nStep 2: Submitting login credentials...\n";

$postFields = [
    '_token' => $csrfToken,
    'email' => 'superadmin@example.com',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Add user agent
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "   HTTP Response Code: $httpCode\n";

// Show response headers for debugging
echo "   Response headers:\n";
foreach (explode("\r\n", $headers) as $header) {
    if (stripos($header, 'Set-Cookie') !== false) {
        echo "      $header\n";
    }
}

// Check for redirect (successful login)
if ($httpCode === 302 || $httpCode === 303) {
    // Extract redirect location
    if (preg_match('/Location:\s*(.+)/i', $headers, $matches)) {
        $redirectUrl = trim($matches[1]);
        echo "   ✅ Login SUCCESS! Redirecting to: $redirectUrl\n";
    } else {
        echo "✅ Login SUCCESS! (HTTP $httpCode redirect)\n";
    }
    
    // Verify session cookie exists
    if (file_exists($cookieFile)) {
        $cookies = file_get_contents($cookieFile);
        if (strpos($cookies, 'laravel_session') !== false) {
            echo "   ✅ Session cookie created!\n";
        } else {
            echo "   ⚠️  No laravel_session cookie found!\n";
        }
    }
} elseif ($httpCode === 419) {
    echo "❌ CSRF Token Mismatch (HTTP 419)\n";
    echo "   This usually means the session wasn't properly maintained.\n";
    echo "   Body preview: " . substr($body, 0, 500) . "\n";
} elseif ($httpCode === 200) {
    // Check for error messages in the response
    if (strpos($body, 'incorrect') !== false || strpos($body, 'invalid') !== false) {
        echo "❌ Login FAILED - Invalid credentials!\n";
    } elseif (strpos($body, 'errors') !== false) {
        echo "❌ Login FAILED - Validation errors found!\n";
        echo "   Body: " . substr($body, 0, 1000) . "\n";
    } else {
        echo "⚠️  Login returned 200 (page displayed). Checking content...\n";
        echo "   Body: " . substr($body, 0, 500) . "\n";
    }
} else {
    echo "⚠️  Unexpected HTTP code: $httpCode\n";
    echo "   Body: " . substr($body, 0, 500) . "\n";
}

// Step 3: Test accessing a protected page with the session
echo "\nStep 3: Testing session by accessing dashboard...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboardUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$dashboardResponse = curl_exec($ch);
$dashboardHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Dashboard HTTP Code: $dashboardHttpCode\n";

if ($dashboardHttpCode === 200) {
    // Check if we're logged in (looking for user info in the response)
    if (strpos($dashboardResponse, 'superadmin') !== false || 
        strpos($dashboardResponse, 'Super Admin') !== false ||
        strpos($dashboardResponse, 'Dashboard') !== false) {
        echo "   ✅ Successfully authenticated! Dashboard is accessible.\n";
    } else {
        echo "   ⚠️  Dashboard accessible but user info not found.\n";
        echo "   Content preview: " . substr($dashboardResponse, 0, 500) . "\n";
    }
} elseif ($dashboardHttpCode === 302 || $dashboardHttpCode === 303) {
    echo "   ❌ Not authenticated - redirected to login.\n";
} else {
    echo "   ⚠️  Unexpected dashboard response: $dashboardHttpCode\n";
}

// Clean up
@unlink($cookieFile);

echo "\n=== Summary ===\n";
if ($httpCode === 302 || $httpCode === 303) {
    echo "✅ Login HTTP endpoint: WORKING\n";
    echo "✅ Authentication: SUCCESS\n";
    echo "\n🚀 The login system is fully functional!\n";
    echo "   Access the application at: $loginUrl\n";
    echo "   Use credentials: superadmin@example.com / password123\n";
} else {
    echo "❌ Login HTTP endpoint: FAILED (HTTP $httpCode)\n";
}
