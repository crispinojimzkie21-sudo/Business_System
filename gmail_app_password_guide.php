<?php

echo "=== Gmail App Password Setup Guide ===\n\n";

echo "🔍 ISSUE IDENTIFIED:\n";
echo "Authentication failed with your current credentials.\n";
echo "This means you need to generate a proper App Password.\n\n";

echo "📋 STEP-BY-STEP FIX:\n\n";

echo "1️⃣ Enable 2-Factor Authentication:\n";
echo "   Go to: https://myaccount.google.com/security\n";
echo "   Enable '2-Step Verification'\n\n";

echo "2️⃣ Generate App Password:\n";
echo "   Go to: https://myaccount.google.com/apppasswords\n";
echo "   Select app: 'Mail'\n";
echo "   Select device: 'Other (Custom name)'\n";
echo "   Enter: 'Manliquid Store'\n";
echo "   Click 'Generate'\n";
echo "   Copy the 16-character password (NO SPACES)\n\n";

echo "3️⃣ Update Configuration:\n";
echo "   Run: php configure_gmail.php\n";
echo "   Or run: php update_gmail_config.php again\n";
echo "   Enter your Gmail and NEW App Password\n\n";

echo "⚠️  IMPORTANT NOTES:\n";
echo "• App Password is DIFFERENT from your regular password\n";
echo "• Must be exactly 16 characters\n";
echo "• No spaces or extra characters\n";
echo "• 2-Factor Authentication MUST be enabled\n\n";

echo "🔧 Current Configuration Status:\n";
echo "✅ Gmail address: manliquidstore@gmail.com\n";
echo "❌ Authentication: Failed (need new App Password)\n\n";

echo "🎯 NEXT ACTIONS:\n";
echo "1. Generate new App Password from Google\n";
echo "2. Run configuration script again\n";
echo "3. Test email sending\n\n";

echo "💡 TIP: If you already have an App Password,\n";
echo "   make sure you're entering it exactly as shown\n";
echo "   (16 characters, no spaces)\n\n";

echo "📞 When ready, run: php configure_gmail.php\n";
