<?php

echo "=== Manliquid Store Gmail SMTP Configuration ===\n\n";

// Correct configuration for Manliquid Store
$manliquidConfig = [
    '# === Gmail SMTP Configuration for Manliquid Store ===',
    'MAIL_MAILER=smtp',
    'MAIL_HOST=smtp.gmail.com',
    'MAIL_PORT=587',
    'MAIL_USERNAME=your-gmail@gmail.com', // Replace with your actual Gmail
    'MAIL_PASSWORD=jycm lnkq qpkg gaom', // Your App Password
    'MAIL_ENCRYPTION=tls',
    'MAIL_FROM_ADDRESS=your-gmail@gmail.com', // Replace with your actual Gmail
    'MAIL_FROM_NAME=Manliquid Store',
    '',
    '# === Important ===',
    '# 1. Replace your-gmail@gmail.com with your actual Gmail address',
    '# 2. App Password is already set: jycm lnkq qpkg gaom',
    '# 3. Make sure 2-Step Verification is enabled',
    '# 4. Port 587 with TLS encryption',
    '',
];

echo "STEP 1: Add this to your .env file:\n\n";
foreach ($manliquidConfig as $line) {
    echo "$line\n";
}

echo "\n=== Example with Your Gmail ===\n";
echo "If your Gmail is: manliquid.store@gmail.com\n\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=manliquid.store@gmail.com\n";
echo "MAIL_PASSWORD=jycm lnkq qpkg gaom\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=manliquid.store@gmail.com\n";
echo "MAIL_FROM_NAME=Manliquid Store\n\n";

echo "=== Update Steps ===\n";
echo "1. Open your .env file\n";
echo "2. Find the mail configuration section\n";
echo "3. Replace with the settings above\n";
echo "4. Replace 'your-gmail@gmail.com' with your actual Gmail\n";
echo "5. Save the .env file\n\n";

echo "=== Clear Cache ===\n";
echo "Run these commands:\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan config:cache\n\n";

echo "=== Test Email ===\n";
echo "Create test_email.php:\n\n";
echo '<?php
require_once __DIR__ . "/vendor/autoload.php";

$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing Manliquid Store Gmail SMTP...\n";

try {
    Mail::raw("Test email from Manliquid Store at " . date("Y-m-d H:i:s"), function($message) {
        $message->to("test@example.com") // Change to your test email
               ->subject("✅ Manliquid Store - Email Test")
               ->from(config("mail.from.address"), "Manliquid Store");
    });
    
    echo "✅ SUCCESS: Email sent from Manliquid Store!\n";
    echo "Check your inbox for the test email\n";
    
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n";
    echo "\nCheck these:\n";
    echo "1. Gmail address is correct\n";
    echo "2. App Password: jycm lnkq qpkg gaom\n";
    echo "3. 2-Step Verification enabled\n";
    echo "4. Port 587, TLS encryption\n";
}
';

echo "\n=== Final Checklist ===\n";
echo "□ Gmail address updated in .env\n";
echo "□ App Password: jycm lnkq qpkg gaom\n";
echo "□ MAIL_FROM_NAME=Manliquid Store\n";
echo "□ 2-Step Verification enabled\n";
echo "□ Config cache cleared\n";
echo "□ Test email sent successfully\n\n";

echo "=== Done! Manliquid Store email ready! ===\n";
