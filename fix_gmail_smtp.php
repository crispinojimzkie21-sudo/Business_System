<?php
/**
 * Gmail SMTP Configuration Script
 * 
 * This script updates the .env file with proper Gmail SMTP settings
 * to help emails land in inbox instead of spam.
 * 
 * Instructions:
 * 1. Edit the configuration below with your Gmail credentials
 * 2. Enable 2-Factor Authentication on your Google Account
 * 3. Generate an App Password: https://myaccount.google.com/apppasswords
 * 4. Run: php fix_gmail_smtp.php
 */

// ============================================
// CONFIGURATION - EDIT THESE VALUES
// ============================================

// Your Gmail address (e.g., mybusiness@gmail.com)
$gmailEmail = 'kenjiedequiroz.08@gmail.com';

// Your Gmail App Password (16 characters, NOT your regular password)
// Get it from: https://myaccount.google.com/apppasswords
// Note: You MUST enable 2-Factor Authentication first to see App Password option
$gmailAppPassword = 'crtgmifskfxuylud';

// Your business name (shown in email sender name)
$businessName = 'Manliquid Store';

// ============================================
// DO NOT EDIT BELOW THIS LINE
// ============================================

// Validate inputs
if ($gmailEmail === 'your-email@gmail.com' || $gmailAppPassword === 'xxxx xxxx xxxx xxxx') {
    echo "===============================================\n";
    echo "❌ Please EDIT this file with your Gmail credentials first!\n";
    echo "===============================================\n\n";
    echo "Edit these variables near line 22-23:\n";
    echo "  \$gmailEmail = 'your-email@gmail.com';\n";
    echo "  \$gmailAppPassword = 'your-16-char-app-password';\n\n";
    echo "To get an App Password:\n";
    echo "  1. Go to https://myaccount.google.com/\n";
    echo "  2. Enable 2-Factor Authentication\n";
    echo "  3. Go to https://myaccount.google.com/apppasswords\n";
    echo "  4. Generate a new app password for 'Mail'\n";
    echo "  5. Copy the 16-character password (format: xxxx xxxx xxxx xxxx)\n\n";
    echo "Then run: php fix_gmail_smtp.php\n";
    exit(1);
}

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Update mail mailer to smtp
$envContent = updateEnvValue($envContent, 'MAIL_MAILER', 'smtp');

// Update mail host (Gmail SMTP)
$envContent = updateEnvValue($envContent, 'MAIL_HOST', 'smtp.gmail.com');

// Update mail port (587 for TLS)
$envContent = updateEnvValue($envContent, 'MAIL_PORT', '587');

// Update mail username (your Gmail)
$envContent = updateEnvValue($envContent, 'MAIL_USERNAME', $gmailEmail);

// Update mail password (App Password)
$envContent = updateEnvValue($envContent, 'MAIL_PASSWORD', $gmailAppPassword);

// Update mail encryption
$envContent = updateEnvValue($envContent, 'MAIL_ENCRYPTION', 'tls');

// Update from address (use gmail email)
$envContent = updateEnvValue($envContent, 'MAIL_FROM_ADDRESS', $gmailEmail);

// Update from name (wrap in quotes for spaces)
$envContent = updateEnvValue($envContent, 'MAIL_FROM_NAME', '"' . $businessName . '"');

// Also update APP_URL if needed
$appUrl = 'http://localhost:8000';
if (!preg_match('/^APP_URL=/m', $envContent)) {
    $envContent .= "\nAPP_URL=$appUrl\n";
}

file_put_contents($envFile, $envContent);

echo "===============================================\n";
echo "✅ Gmail SMTP configuration updated successfully!\n";
echo "===============================================\n\n";
echo "Configuration applied:\n";
echo "----------------------\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=$gmailEmail\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=$gmailEmail\n";
echo "MAIL_FROM_NAME=$businessName\n\n";

echo "⚠️  IMPORTANT STEPS:\n";
echo "--------------------\n";
echo "1. ✅ Configuration has been saved to .env file\n";
echo "2. Run: php artisan config:clear\n";
echo "3. Run: php artisan cache:clear\n\n";

echo "📧 To test email sending, run:\n";
echo "   php artisan tinker\n";
echo "   Mail::raw('Test Email', function(\$msg) { \$msg->to('$gmailEmail')->subject('Test'); });\n";

function updateEnvValue($content, $key, $value) {
    if (preg_match("/^$key=/m", $content)) {
        $content = preg_replace("/^$key=.*$/m", "$key=$value", $content);
    } else {
        $content .= "\n$key=$value";
    }
    return $content;
}

