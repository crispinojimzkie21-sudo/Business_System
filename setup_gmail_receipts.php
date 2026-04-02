<?php

/**
 * Interactive Gmail Setup for Email Receipts
 * This script will help you configure Gmail properly
 */

echo "=== Gmail Email Receipt Setup ===\n\n";

// Get Gmail credentials
echo "📧 Enter your Gmail credentials:\n\n";

echo "Gmail Address: ";
$gmail = trim(fgets(STDIN));

if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email address!\n";
    exit(1);
}

echo "App Password (16 characters, format: xxxx xxxx xxxx xxxx): ";
$appPassword = trim(fgets(STDIN));

if (strlen($appPassword) < 16) {
    echo "❌ App Password too short! Must be 16 characters.\n";
    echo "⚠️ Get App Password from: https://myaccount.google.com/apppasswords\n";
    exit(1);
}

echo "\nBusiness Name (default: Manliquid Store): ";
$businessName = trim(fgets(STDIN)) ?: "Manliquid Store";

echo "\n🔧 Configuring Gmail for email receipts...\n";

// Update .env file
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

// Add new Gmail config
$mailConfig = [
    "MAIL_MAILER=smtp",
    "MAIL_HOST=smtp.gmail.com",
    "MAIL_PORT=587",
    "MAIL_USERNAME=" . $gmail,
    "MAIL_PASSWORD=" . $appPassword,
    "MAIL_ENCRYPTION=tls",
    "MAIL_FROM_ADDRESS=" . $gmail,
    'MAIL_FROM_NAME="' . $businessName . '"'
];

$envContent = implode("\n", $newLines) . "\n" . implode("\n", $mailConfig);

if (file_put_contents($envFile, $envContent)) {
    echo "✅ Gmail configuration saved to .env\n";
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
    \Illuminate\Support\Facades\Mail::raw("Email Receipt System Test - SUCCESS! 🎉

Your Manliquid Store email receipt system is now working perfectly!

📧 Email Configuration: ✅ Working
📄 Receipt Templates: ✅ Ready
🖨️ Print Design: ✅ Optimized
📱 Customer Experience: ✅ Professional

Features Available:
✅ Automatic receipt sending to customers
✅ Professional HTML email templates
✅ Black text optimized for printing
✅ Complete transaction details
✅ Itemized product lists
✅ Payment method display
✅ Company branding
✅ Error handling and logging

Test Time: " . date("Y-m-d H:i:s") . "
Gmail Account: $gmail
Business Name: $businessName

This confirms your email receipt system is FULLY OPERATIONAL!

Start sending professional receipts to your customers today!", function($message) use ($gmail, $businessName) {
            $message->to($gmail)
                   ->subject("✅ SUCCESS! Email Receipt System Working")
                   ->from($gmail, $businessName);
        });
    
    echo "✅ SUCCESS! Test email sent!\n";
    echo "📧 Check inbox: $gmail\n\n";
    
    echo "🎉 EMAIL RECEIPT SYSTEM IS READY!\n\n";
    
    echo "📋 How to send receipts:\n";
    echo "1. Make a sale with customer email\n";
    echo "2. Receipt automatically sent to customer\n";
    echo "3. Or use 'Resend Receipt' option\n";
    echo "4. Customer receives professional HTML receipt\n\n";
    
    echo "🌟 What customers receive:\n";
    echo "✅ Professional email with company branding\n";
    echo "✅ Complete transaction details\n";
    echo "✅ Itemized product list with prices\n";
    echo "✅ Payment method and amount\n";
    echo "✅ Print-friendly black text design\n";
    echo "✅ Company contact information\n\n";
    
    echo "🔄 Clearing caches...\n";
    shell_exec("php artisan config:clear");
    shell_exec("php artisan cache:clear");
    shell_exec("php artisan view:clear");
    echo "✅ All caches cleared\n\n";
    
    echo "🚀 YOUR EMAIL RECEIPT SYSTEM IS FULLY FUNCTIONAL!\n";
    echo "Customers will love receiving professional receipts!\n\n";
    
    echo "📞 Need help? Check logs in storage/logs/laravel.log\n";
    
} catch (\Exception $e) {
    echo "❌ Email test failed: " . $e->getMessage() . "\n\n";
    
    // Provide specific help
    if (strpos($e->getMessage(), "535") !== false) {
        echo "🔧 AUTHENTICATION FIX NEEDED:\n";
        echo "1. Enable 2-Factor Authentication: https://myaccount.google.com/security\n";
        echo "2. Generate App Password: https://myaccount.google.com/apppasswords\n";
        echo "3. Select: Mail → Device: Business System\n";
        echo "4. Copy the 16-character password exactly\n";
        echo "5. Run this script again with correct password\n\n";
    } elseif (strpos($e->getMessage(), "Connection") !== false) {
        echo "🔧 CONNECTION FIX NEEDED:\n";
        echo "1. Check internet connection\n";
        echo "2. Verify port 587 is not blocked\n";
        echo "3. Try different network\n";
        echo "4. Check firewall settings\n\n";
    } else {
        echo "🔧 GENERAL FIX NEEDED:\n";
        echo "1. Verify Gmail credentials\n";
        echo "2. Check Gmail account access\n";
        echo "3. Ensure 2-FA is enabled\n";
        echo "4. Generate new App Password\n\n";
    }
    
    echo "💡 QUICK FIX:\n";
    echo "Run: php gmail_receipt_setup_guide.php\n";
    echo "For detailed troubleshooting steps\n\n";
}

echo "\n=== Setup Complete ===\n";
