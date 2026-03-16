<?php

/**
 * Complete Gmail SMTP Configuration
 */

echo "=== Gmail SMTP Configuration ===\n\n";

// Instructions
echo "📋 SETUP INSTRUCTIONS:\n";
echo "1. Enable 2-Factor Authentication on your Gmail account\n";
echo "2. Go to: https://myaccount.google.com/apppasswords\n";
echo "3. Generate App Password for 'Mail' app\n";
echo "4. Copy the 16-character password\n\n";

// Get user input
echo "📧 Enter your Gmail address: ";
$gmail = trim(fgets(STDIN));

echo "🔑 Enter App Password (16 characters): ";
$password = trim(fgets(STDIN));

if (strlen($password) !== 16) {
    echo "❌ App Password should be 16 characters!\n";
    exit(1);
}

// Update .env file
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Remove existing mail config
$lines = explode("\n", $envContent);
$newLines = [];
foreach ($lines as $line) {
    if (!str_starts_with($line, 'MAIL_')) {
        $newLines[] = $line;
    }
}

// Add new Gmail SMTP config
$mailConfig = [
    'MAIL_MAILER=smtp',
    'MAIL_HOST=smtp.gmail.com',
    'MAIL_PORT=587',
    'MAIL_USERNAME=' . $gmail,
    'MAIL_PASSWORD=' . $password,
    'MAIL_ENCRYPTION=tls',
    'MAIL_FROM_ADDRESS=' . $gmail,
    'MAIL_FROM_NAME="Business System"'
];

$envContent = implode("\n", $newLines) . "\n" . implode("\n", $mailConfig);

if (file_put_contents($envFile, $envContent)) {
    echo "✅ Configuration updated successfully!\n\n";
    
    echo "📋 Configuration Added:\n";
    foreach ($mailConfig as $config) {
        echo "  $config\n";
    }
    
    echo "\n🧪 Next Steps:\n";
    echo "1. Run: php test_gmail_smtp.php\n";
    echo "2. Clear caches: php artisan config:clear\n";
    echo "3. Test email receipt functionality\n\n";
    
    echo "🎉 Your email receipts should now work!\n";
} else {
    echo "❌ Failed to update .env file!\n";
}
