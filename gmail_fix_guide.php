<?php

echo "=== Gmail SMTP Fix Script ===\n\n";

// Step 1: Create proper .env configuration
$envConfig = [
    '# === Gmail SMTP Configuration ===',
    'MAIL_MAILER=smtp',
    'MAIL_HOST=smtp.gmail.com',
    'MAIL_PORT=587',
    'MAIL_USERNAME=your-gmail@gmail.com',
    'MAIL_PASSWORD=your-16-digit-app-password',
    'MAIL_ENCRYPTION=tls',
    'MAIL_FROM_ADDRESS=your-gmail@gmail.com',
    'MAIL_FROM_NAME=Business System',
    '',
    '# === Important Notes ===',
    '# 1. Use App Password (16 characters), NOT regular password',
    '# 2. Enable 2-Step Verification first',
    '# 3. Generate App Password from Google Account settings',
    '# 4. Port 587 with TLS (not SSL)',
    '',
];

echo "STEP 1: Replace your .env mail configuration with:\n\n";
foreach ($envConfig as $line) {
    echo "$line\n";
}

echo "\n=== Gmail App Password Setup ===\n";
echo "1. Go to: https://myaccount.google.com/\n";
echo "2. Click: Security → 2-Step Verification\n";
echo "3. Turn ON 2-Step Verification\n";
echo "4. Click: App passwords\n";
echo "5. Select: Mail → Device: Business System\n";
echo "6. Copy the 16-character password (no spaces)\n";
echo "7. Use this password in MAIL_PASSWORD\n\n";

echo "=== Fix Common Errors ===\n\n";

echo "❌ ERROR: Authentication failed\n";
echo "✅ SOLUTION: Use App Password (16 chars), not regular password\n";
echo "   - Regular Gmail password won't work with SMTP\n";
echo "   - Must generate App Password from Google Account\n\n";

echo "❌ ERROR: Connection timeout\n";
echo "✅ SOLUTION: Check these settings:\n";
echo "   - Port: 587 (not 465 or 25)\n";
echo "   - Encryption: tls (not ssl)\n";
echo "   - Host: smtp.gmail.com\n";
echo "   - Firewalls: Allow outbound port 587\n\n";

echo "❌ ERROR: Email goes to spam\n";
echo "✅ SOLUTION: Improve email quality:\n";
echo "   - Use real Gmail address in MAIL_FROM_ADDRESS\n";
echo "   - Set proper MAIL_FROM_NAME\n";
echo "   - Avoid spammy content in emails\n";
echo "   - Add proper HTML structure\n\n";

echo "❌ ERROR: SSL/TLS errors\n";
echo "✅ SOLUTION: Use TLS, not SSL:\n";
echo "   - MAIL_ENCRYPTION=tls\n";
echo "   - Port 587 (TLS port)\n";
echo "   - Port 465 (SSL port - don't use)\n\n";

echo "=== Working .env Example ===\n";
echo "# Copy this EXACTLY (replace email and password):\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=business.system@gmail.com\n";
echo "MAIL_PASSWORD=abcd efgh ijkl mnop\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=business.system@gmail.com\n";
echo "MAIL_FROM_NAME=Business System\n\n";

echo "=== Done! ===\n";
