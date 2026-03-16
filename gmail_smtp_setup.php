<?php

echo "=== Gmail SMTP Configuration Setup ===\n\n";

// Gmail SMTP Configuration
$gmailConfig = [
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'smtp.gmail.com',
    'MAIL_PORT' => '587',
    'MAIL_USERNAME' => 'your-gmail-address@gmail.com', // Replace with your Gmail
    'MAIL_PASSWORD' => 'your-app-password', // Replace with your App Password
    'MAIL_ENCRYPTION' => 'tls',
    'MAIL_FROM_ADDRESS' => 'your-gmail-address@gmail.com', // Replace with your Gmail
    'MAIL_FROM_NAME' => 'Business System',
];

echo "Add these lines to your .env file:\n\n";
echo "# Gmail SMTP Configuration\n";
foreach ($gmailConfig as $key => $value) {
    echo "$key=$value\n";
}

echo "\n=== Gmail Setup Instructions ===\n";
echo "1. Enable 2-Step Verification in your Gmail account\n";
echo "2. Create an App Password:\n";
echo "   - Go to Google Account settings\n";
echo "   - Security → 2-Step Verification → App passwords\n";
echo "   - Generate new app password for 'Business System'\n";
echo "3. Replace 'your-gmail-address@gmail.com' with your actual Gmail\n";
echo "4. Replace 'your-app-password' with the generated app password\n";
echo "5. Add the configuration to your .env file\n";
echo "6. Run: php artisan config:cache\n";
echo "7. Run: php artisan config:clear\n\n";

echo "=== Test Email Script ===\n";
echo "Create this test script to verify email sending:\n\n";
echo '<?php
require_once __DIR__ . "/vendor/autoload.php";

$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw("Test email from Business System", function($message) {
        $message->to("test@example.com")
               ->subject("SMTP Test")
               ->from("your-gmail-address@gmail.com");
    });
    echo "✅ Email sent successfully!";
} catch (Exception $e) {
    echo "❌ Email failed: " . $e->getMessage();
}
';

echo "\n=== Common Gmail Issues & Solutions ===\n";
echo "Issue: 'SMTP Error: Could not authenticate'\n";
echo "Solution: Use App Password, not regular password\n\n";
echo "Issue: 'Connection timed out'\n";
echo "Solution: Check port 587, enable TLS, check firewall\n\n";
echo "Issue: 'Email goes to spam'\n";
echo "Solution: Use proper from address, avoid spammy content\n\n";

echo "=== Complete .env Example ===\n";
echo "# Add to your existing .env file:\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=your-gmail-address@gmail.com\n";
echo "MAIL_PASSWORD=your-app-password\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=your-gmail-address@gmail.com\n";
echo "MAIL_FROM_NAME=Business System\n\n";

echo "=== Done! ===\n";
