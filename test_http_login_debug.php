<?php
/**
 * Test HTTP Login Flow - Debug Version
 * Tests the actual login via HTTP request with more debugging
 */

$loginUrl = 'http://127.0.0.1:8000/login';
$csrfUrl = 'http://127.0.0.1:8000/login';

echo "=== Testing HTTP Login Flow (Debug) ===\n\n";

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
    exit(1);
}

echo "   HTTP Response Code: $httpCode\n";
echo "   Response headers (first 500 chars):\n" . substr($headers, 0, 500) . "\n";

// Check for session cookie in headers
if (preg_match('/Set-Cookie:.*laravel_session=([^;]+)/', $headers, $matches)) {
    echo "   ✅ Session cookie found: " . substr($matches[1], 0, 30) . "...\n";
} else {
    echo "   ⚠️  No session cookie found in headers!\n";
}

// Check cookies file
if (file_exists(__DIR__ . '/cookies.txt')) {
    $cookies = file_get_contents(__DIR__ . '/cookies.txt');
    echo "   Cookies file content:\n" . $cookies . "\n";
}

if ($httpCode !== 200) {
    echo "❌ Failed to fetch login page. HTTP Code: $httpCode\n";
    exit(1);
}

echo "✅ Login page fetched successfully (HTTP $httpCode)\n";

// Extract CSRF token from the response
if (preg_match('/name="_token"[^>]*value="([^"]+)"/', $body, $matches)) {
    $csrfToken = $matches[1];
    echo "   CSRF Token (form): " . substr($csrfToken, 0, 20) . "...\n";
} elseif (preg_match('/csrf-token"[^>]*content="([^"]+)"/', $body, $matches)) {
    $csrfToken = $matches[1];
    echo "   CSRF Token (meta): " . substr($csrfToken, 0, 20) . "...\n";
} else {
    echo "❌ Could not find CSRF token in the response!\n";
    // Try to show a snippet of the response
    echo "   Response snippet: " . substr($body, 0, 500) . "\n";
    exit(1);
}

// Step 2: Submit login form
echo "\nStep 2: Submitting login credentials...\n";

$postFields = [
    '_token' => $csrfToken,
    'email' => 'superadmin@example.com',
    'password' => 'password123'
];

echo "   POST data: " . http_build_query($postFields) . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "   HTTP Response Code: $httpCode\n";
echo "   Response headers:\n" . substr($headers, 0, 500) . "\n";

// Check cookies after login
if (file_exists(__DIR__ . '/cookies.txt')) {
    $cookies = file_get_contents(__DIR__ . '/cookies.txt');
    echo "   Cookies after login:\n" . $cookies . "\n";
}

// Check for redirect (successful login)
if ($httpCode === 302) {
    // Extract redirect location
    if (preg_match('/Location:\s*(.+)/i', $headers, $matches)) {
        $redirectUrl = trim($matches[1]);
        echo "   ✅ Login SUCCESS! Redirecting to: $redirectUrl\n";
    } else {
        echo "✅ Login SUCCESS! (HTTP 302 redirect)\n";
    }
} elseif ($httpCode === 419) {
    echo "   ❌ CSRF Token Mismatch (HTTP 419)\n";
    echo "   This could mean:\n";
    echo "   - Session is not being persisted\n";
    echo "   - CSRF token validation failed\n";
} elseif ($httpCode === 200) {
    echo "   Response body (first 500 chars):\n" . substr($body, 0, 500) . "\n";
}

// Clean up
@unlink(__DIR__ . '/cookies.txt');

echo "\n=== Test Complete ===\n";

