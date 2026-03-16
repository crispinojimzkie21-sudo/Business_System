<?php

/**
 * Gmail SMTP Configuration Setup
 * 
 * This script will help you configure Gmail SMTP for sending email receipts.
 * Follow the instructions below to set up your Gmail account for SMTP.
 */

echo "=== Gmail SMTP Configuration Setup ===\n\n";

echo "STEP 1: Enable 2-Factor Authentication on your Gmail account\n";
echo "1. Go to https://myaccount.google.com/security\n";
echo "2. Enable 2-Step Verification\n\n";

echo "STEP 2: Generate an App Password\n";
echo "1. Go to https://myaccount.google.com/apppasswords\n";
echo "2. Select 'Mail' for the app\n";
echo "3. Select 'Other (Custom name)' and enter 'Business System'\n";
echo "4. Click 'Generate'\n";
echo "5. Copy the 16-character password (without spaces)\n\n";

echo "STEP 3: Configure Environment Variables\n";
echo "Please provide the following information:\n\n";

// Get user input
echo "Enter your Gmail address: ";
$gmail_address = trim(fgets(STDIN));

echo "Enter the 16-character App Password (copy from step 2): ";
$app_password = trim(fgets(STDIN));

echo "Enter your application name (default: Business System): ";
$app_name = trim(fgets(STDIN)) ?: 'Business System';

// Read current .env file
$env_file = '.env';
if (!file_exists($env_file)) {
    echo "Error: .env file not found!\n";
    exit(1);
}

$env_content = file_get_contents($env_file);

// Update mail configuration
$mail_config = [
    'MAIL_MAILER=smtp',
    'MAIL_HOST=smtp.gmail.com',
    'MAIL_PORT=587',
    'MAIL_USERNAME=' . $gmail_address,
    'MAIL_PASSWORD=' . $app_password,
    'MAIL_ENCRYPTION=tls',
    'MAIL_FROM_ADDRESS=' . $gmail_address,
    'MAIL_FROM_NAME="' . $app_name . '"'
];

// Remove existing mail configuration lines
$lines = explode("\n", $env_content);
$new_lines = [];
$skip_next = false;

foreach ($lines as $line) {
    if (str_starts_with($line, 'MAIL_')) {
        continue; // Skip existing mail config
    }
    $new_lines[] = $line;
}

// Add new mail configuration
$env_content = implode("\n", $new_lines) . "\n" . implode("\n", $mail_config);

// Write back to .env file
if (file_put_contents($env_file, $env_content)) {
    echo "\n✅ Gmail SMTP configuration updated successfully!\n\n";
    
    echo "Configuration added to .env file:\n";
    foreach ($mail_config as $config) {
        echo "  $config\n";
    }
    
    echo "\nSTEP 4: Test the configuration\n";
    echo "Run the following command to test email sending:\n";
    echo "php artisan test_gmail_smtp\n\n";
    
    echo "STEP 5: Clear caches\n";
    echo "Run: php artisan config:clear\n";
    echo "Run: php artisan cache:clear\n\n";
    
    echo "Your email receipts should now work! 🎉\n";
    
} else {
    echo "❌ Failed to update .env file. Please check file permissions.\n";
}
