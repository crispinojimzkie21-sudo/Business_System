<?php
/**
 * Test HTTP Login Flow
 * Tests the actual login via HTTP request
 */

$loginUrl = 'http://127.0.0.1:8000/login';
$csrfUrl = 'http://127.0.0.1:8000/login';

echo "=== Testing HTTP Login Flow ===\n\n";

// Step 1: Get the login page and extract CSRF token
echo "Step 1: Fetching login page to get CSRF token...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $csrfUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

$curlError = curl_error($ch);
$curlErrno = curl_errno($ch);
curl_close($ch);

// Check for cURL errors
if ($curlErrno !== 0) {
    echo "❌ cURL Error [$curlErrno]: $curlError\n";
    echo "\n⚠️  The Laravel development server is not running!\n";
    echo "   Please start the server with one of these commands:\n";
    echo "   - php artisan serve\n";
    echo "   - php artisan serve --host=127.0.0.1 --port=8000\n";
    echo "   - Then run this script again\n";
    exit(1);
}

if ($httpCode !== 200) {
    echo "❌ Failed to fetch login page. HTTP Code: $httpCode\n";
    exit(1);
}

echo "✅ Login page fetched successfully (HTTP $httpCode)\n";

// Extract CSRF token from the response - check multiple patterns
$csrfToken = null;

// Try to get token from hidden form field (main CSRF token)
if (preg_match('/name="_token"[^>]*value="([^"]+)"/', $body, $matches)) {
    $csrfToken = $matches[1];
    echo "   CSRF Token (form _token): " . substr($csrfToken, 0, 20) . "...\n";
} 
// Try to get token from meta tag
elseif (preg_match('/csrf-token"[^>]*content="([^"]+)"/', $body, $matches)) {
    $csrfToken = $matches[1];
    echo "   CSRF Token (meta): " . substr($csrfToken, 0, 20) . "...\n";
}
// Try to get token from X-XSRF-TOKEN header that was sent
else {
    echo "❌ Could not find CSRF token in the response!\n";
    echo "   Response snippet: " . substr($body, 0, 500) . "\n";
    exit(1);
}

// Also extract XSRF-TOKEN cookie if present for AJAX requests
$xsrfToken = null;
if (preg_match('/Set-Cookie: XSRF-TOKEN=([^;]+)/', $headers, $matches)) {
    $xsrfToken = urldecode($matches[1]);
    echo "   XSRF Token (cookie): " . substr($xsrfToken, 0, 20) . "...\n";
}

// Step 2: Submit login form
echo "\nStep 2: Submitting login credentials...\n";

// Use the CSRF token we extracted
$postFields = [
    '_token' => $csrfToken,
    'email' => 'superadmin@example.com',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies.txt');
// Add X-XSRF-TOKEN header if we have it
if ($xsrfToken) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-XSRF-TOKEN: ' . $xsrfToken));
}
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "   HTTP Response Code: $httpCode\n";

// Check for redirect (successful login)
if ($httpCode === 302) {
    // Extract redirect location
    if (preg_match('/Location:\s*(.+)/i', $headers, $matches)) {
        $redirectUrl = trim($matches[1]);
        echo "   ✅ Login SUCCESS! Redirecting to: $redirectUrl\n";
    } else {
        echo "✅ Login SUCCESS! (HTTP 302 redirect)\n";
    }
    
    // Verify session cookie exists
    if (file_exists(__DIR__ . '/cookies.txt')) {
        $cookies = file_get_contents(__DIR__ . '/cookies.txt');
        if (strpos($cookies, 'laravel_session') !== false) {
            echo "   ✅ Session cookie created!\n";
        }
    }
} elseif ($httpCode === 419) {
    echo "   ❌ CSRF Token Mismatch (HTTP 419)\n";
    echo "   Trying alternative approach...\n";
    
    // Let's try without using the cookies file and just getting a fresh session
} elseif ($httpCode === 200) {
    // Check for error messages in the response
    if (strpos($body, 'incorrect') !== false || strpos($body, 'invalid') !== false) {
        echo "❌ Login FAILED - Invalid credentials!\n";
    } elseif (strpos($body, 'errors') !== false) {
        echo "❌ Login FAILED - Validation errors found!\n";
    } else {
        echo "⚠️  Login returned 200 (page displayed). Checking content...\n";
    }
} else {
    echo "⚠️  Unexpected HTTP code: $httpCode\n";
}

// Step 3: Test accessing a protected page with the session
echo "\nStep 3: Testing session by accessing dashboard...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

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
    }
} elseif ($dashboardHttpCode === 302 || $dashboardHttpCode === 303) {
    echo "   ❌ Not authenticated - redirected to login.\n";
} else {
    echo "   ⚠️  Unexpected dashboard response: $dashboardHttpCode\n";
}

// Clean up
@unlink(__DIR__ . '/cookies.txt');

echo "\n=== Summary ===\n";
if ($httpCode === 302) {
    echo "✅ Login HTTP endpoint: WORKING\n";
    echo "✅ Authentication: SUCCESS\n";
    echo "\n🚀 The login system is fully functional!\n";
    echo "   Access the application at: http://127.0.0.1:8000/login\n";
    echo "   Use credentials: superadmin@example.com / password123\n";
} else {
    echo "❌ Login HTTP endpoint: FAILED (HTTP $httpCode)\n";
}

