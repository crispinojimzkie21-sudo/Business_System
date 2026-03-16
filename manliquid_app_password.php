<?php

echo "=== Gmail App Password Setup for Manliquid Store ===\n\n";

echo "When creating App Password in Google Account:\n\n";

echo "STEP 1: Go to Google Account Settings\n";
echo "https://myaccount.google.com/\n\n";

echo "STEP 2: Enable 2-Step Verification\n";
echo "Security → 2-Step Verification → Turn ON\n\n";

echo "STEP 3: Generate App Password\n";
echo "Click: App passwords\n\n";

echo "STEP 4: Use THESE EXACT SETTINGS:\n";
echo "┌─────────────────────────────────┐\n";
echo "│ Select app:     Mail            │\n";
echo "│ Select device:  Manliquid Store │\n";
echo "└─────────────────────────────────┘\n\n";

echo "STEP 5: Generate and Copy Password\n";
echo "- Click GENERATE\n";
echo "- Copy the 16-character password\n";
echo "- Format: xxxx xxxx xxxx xxxx\n";
echo "- Your password: jycm lnkq qpkg gaom\n\n";

echo "=== IMPORTANT: Use 'Manliquid Store' ===\n\n";
echo "❌ WRONG: Business System\n";
echo "❌ WRONG: Business System App\n";
echo "❌ WRONG: My Business\n\n";
echo "✅ CORRECT: Manliquid Store\n\n";

echo "=== Why This Matters ===\n";
echo "• The app name helps you identify which password is for what\n";
echo "• 'Manliquid Store' matches your MAIL_FROM_NAME\n";
echo "• Consistent branding across all email communications\n";
echo "• Easy to manage multiple app passwords\n\n";

echo "=== Complete Setup Checklist ===\n";
echo "□ 2-Step Verification enabled\n";
echo "□ App password generated for 'Manliquid Store'\n";
echo "□ Password copied: jycm lnkq qpkg gaom\n";
echo "□ .env updated with correct settings\n";
echo "□ MAIL_FROM_NAME=Manliquid Store\n";
echo "□ Config cache cleared\n\n";

echo "=== Final .env Configuration ===\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=your-gmail@gmail.com\n";
echo "MAIL_PASSWORD=jycm lnkq qpkg gaom\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=your-gmail@gmail.com\n";
echo "MAIL_FROM_NAME=Manliquid Store\n\n";

echo "=== Test Your Setup ===\n";
echo "After updating .env, run:\n";
echo "php artisan config:cache\n";
echo "php artisan tinker\n";
echo "Then test: Mail::raw('Test', fn(\$m) => \$m->to('test@example.com'));\n\n";

echo "=== Done! Manliquid Store emails ready! ===\n";
