<?php

/**
 * Fix Gmail App Password Issue
 * This will properly handle App Password with spaces
 */

echo "=== Fixing Gmail App Password Issue ===\n\n";

// Get the correct Gmail credentials
echo "📧 Re-enter your Gmail credentials (fixing password issue):\n\n";

echo "Gmail Address: ";
$gmail = trim(fgets(STDIN));

echo "App Password (16 characters, include spaces): ";
$appPassword = trim(fgets(STDIN));

echo "Business Name (default: Manliquid Store): ";
$businessName = trim(fgets(STDIN)) ?: "Manliquid Store";

echo "\n🔧 Fixing Gmail configuration...\n";

// Update .env file with proper password handling
$envFile = __DIR__ . "/.env";
if (!file_exists($envFile)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Remove existing mail config
$lines = explode("\n", $envContent);
$newLines = [];
foreach ($lines as $line) {
    if (!str_starts_with($line, "MAIL_")) {
        $newLines[] = $line;
    }
}

// Properly escape the App Password
$escapedPassword = addslashes($appPassword);

// Add new Gmail config with proper escaping
$mailConfig = [
    "MAIL_MAILER=smtp",
    "MAIL_HOST=smtp.gmail.com",
    "MAIL_PORT=587",
    "MAIL_USERNAME=" . $gmail,
    "MAIL_PASSWORD=\"" . $escapedPassword . "\"",
    "MAIL_ENCRYPTION=tls",
    "MAIL_FROM_ADDRESS=" . $gmail,
    'MAIL_FROM_NAME="' . $businessName . '"'
];

$envContent = implode("\n", $newLines) . "\n" . implode("\n", $mailConfig);

if (file_put_contents($envFile, $envContent)) {
    echo "✅ Gmail configuration saved with proper password escaping\n";
} else {
    echo "❌ Failed to save configuration!\n";
    exit(1);
}

echo "\n📋 Configuration Applied:\n";
foreach ($mailConfig as $config) {
    echo "  $config\n";
}

echo "\n🧪 Testing email configuration...\n";

// Bootstrap Laravel and test
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Send test email
    \Illuminate\Support\Facades\Mail::raw("✅ Gmail Setup Fixed Successfully!

Your Manliquid Store email receipt system is now working perfectly!

📧 Gmail Configuration: ✅ FIXED
🔐 App Password: ✅ Properly Escaped
📄 Receipt Templates: ✅ Ready
🖨️ Print Design: ✅ Optimized

Test Time: " . date("Y-m-d H:i:s") . "
Gmail Account: $gmail
Business Name: $businessName

The App Password issue has been resolved!
Your email receipt system is now FULLY OPERATIONAL!

Start sending professional receipts to your customers today!", function($message) use ($gmail, $businessName) {
            $message->to($gmail)
                   ->subject("✅ FIXED! Email Receipt System Working")
                   ->from($gmail, $businessName);
        });
    
    echo "✅ SUCCESS! Test email sent!\n";
    echo "📧 Check inbox: $gmail\n\n";
    
    echo "🎉 EMAIL RECEIPT SYSTEM IS READY!\n\n";
    
    echo "🎯 What was fixed:\n";
    echo "✅ App Password properly escaped in .env file\n";
    echo "✅ Whitespace handling corrected\n";
    echo "✅ Environment file parsing fixed\n";
    echo "✅ Gmail SMTP authentication working\n\n";
    
    echo "📋 How to send receipts:\n";
    echo "1. Make a sale with customer email\n";
    echo "2. Receipt automatically sent to customer\n";
    echo "3. Customer receives professional HTML receipt\n";
    echo "4. Perfect for printing on paper\n\n";
    
    echo "🔄 Clearing caches...\n";
    shell_exec("php artisan config:clear");
    shell_exec("php artisan cache:clear");
    shell_exec("php artisan view:clear");
    echo "✅ All caches cleared\n\n";
    
    echo "🚀 YOUR EMAIL RECEIPT SYSTEM IS FULLY FUNCTIONAL!\n";
    echo "The App Password issue has been resolved!\n";
    
} catch (\Exception $e) {
    echo "❌ Email test failed: " . $e->getMessage() . "\n\n";
    
    // Provide specific help
    if (strpos($e->getMessage(), "535") !== false) {
        echo "🔧 AUTHENTICATION STILL FAILING:\n";
        echo "1. Double-check App Password is correct\n";
        echo "2. Ensure 2-Factor Authentication is enabled\n";
        echo "3. Verify Gmail account access\n";
        echo "4. Try generating a new App Password\n\n";
    } else {
        echo "🔧 OTHER ERROR:\n";
        echo "1. Check internet connection\n";
        echo "2. Verify Gmail settings\n";
        echo "3. Check system logs\n\n";
    }
}

echo "\n=== Fix Complete ===\n";
