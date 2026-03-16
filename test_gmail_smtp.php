<?php
/**
 * Test Gmail SMTP Connection
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing Gmail SMTP connection...\n";
echo "================================\n\n";

echo "Current Mail Configuration:\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER', 'not set') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', 'not set') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT', 'not set') . "\n";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME', 'not set') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION', 'not set') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS', 'not set') . "\n\n";

// Get test email address
echo "Enter email address to send test to: ";
$test_email = trim(fgets(STDIN));

if (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email address!\n";
    exit(1);
}

try {
    Mail::raw('Test Email from Business System', function($message) use ($test_email) {
        $message->to($test_email)
                ->subject('Gmail SMTP Test - ' . date('Y-m-d H:i:s'));
    });
    
    echo "✅ SUCCESS! Test email sent successfully!\n";
    echo "Check your inbox (and spam folder) at: $test_email\n";
    
} catch (Exception $e) {
    echo "❌ FAILED! Error message:\n";
    echo $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), '535') !== false) {
        echo "⚠️  Authentication failed!\n";
        echo "This usually means:\n";
        echo "  1. The App Password is incorrect\n";
        echo "  2. 2-Factor Authentication is not enabled on your Google Account\n";
        echo "  3. The App Password was revoked\n\n";
        echo "Please generate a new App Password from:\n";
        echo "https://myaccount.google.com/apppasswords\n";
    } elseif (strpos($e->getMessage(), 'Connection') !== false) {
        echo "⚠️  Connection failed!\n";
        echo "This usually means:\n";
        echo "  1. Mail server settings are incorrect\n";
        echo "  2. Firewall is blocking the connection\n";
        echo "  3. Internet connection issues\n";
    } else {
        echo "⚠️  General error occurred!\n";
        echo "Please check your mail configuration in .env file\n";
    }
}

