# 📧 Email Receipt Setup Guide

## 🎯 Overview
This guide will help you configure Gmail SMTP to send email receipts automatically when sales are made.

## 📋 Prerequisites
- Gmail account with 2-Factor Authentication enabled
- App Password generated from Google Account settings

## 🔧 Step-by-Step Setup

### Step 1: Enable 2-Factor Authentication
1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Enable "2-Step Verification"
3. Follow the setup process

### Step 2: Generate App Password
1. Go to [App Passwords](https://myaccount.google.com/apppasswords)
2. Select "Mail" for the app
3. Select "Other (Custom name)" 
4. Enter "Business System" as the name
5. Click "Generate"
6. **Copy the 16-character password** (without spaces)

### Step 3: Configure Gmail SMTP
Run the configuration script:
```bash
php configure_gmail.php
```

Enter:
- Your Gmail address
- The 16-character App Password

### Step 4: Test Email Configuration
```bash
php test_gmail_smtp.php
```

Enter any email address to send a test email.

### Step 5: Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
```

## 📧 How Email Receipts Work

### Automatic Sending
- When a sale is completed with a customer email
- System automatically sends a receipt to the customer
- Receipt includes transaction details and items purchased

### Manual Resending
- Go to Sales History
- Click "Resend Receipt" for any sale
- Email will be sent to the customer's email address

### Email Template Features
- Professional HTML design
- Company branding
- Transaction details
- Itemized list of products
- Total amount
- Link to view full receipt online

## 🛠️ Configuration Details

The setup script adds these lines to your `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="Business System"
```

## 🔍 Troubleshooting

### Common Issues

#### Authentication Failed (535)
- Enable 2-Factor Authentication on your Gmail account
- Generate a new App Password
- Ensure you're using the 16-character password (not your regular password)

#### Connection Timeout
- Check internet connection
- Verify firewall settings
- Ensure port 587 is not blocked

#### Email Not Received
- Check spam/junk folder
- Verify email address is correct
- Test with different email addresses

### Test Commands

#### Test SMTP Connection
```bash
php test_gmail_smtp.php
```

#### Check Mail Configuration
```bash
php artisan tinker
>>> echo config('mail.mailers.smtp.host');
>>> echo config('mail.mailers.smtp.port');
>>> echo config('mail.from.address');
```

## 🎉 Success Indicators

### ✅ Setup Successful When:
- Test email sends successfully
- Email appears in inbox (check spam folder)
- No authentication errors
- Sales receipts send automatically

### 📧 Email Features Working:
- Automatic receipt sending on sales
- Professional email template
- Proper formatting and styling
- All transaction details included

## 🔄 Maintenance

### Regular Tasks:
- Monitor email sending logs
- Update App Password if changed
- Check spam folder for delivery issues
- Test email functionality periodically

### Security Notes:
- Never share your App Password
- Regularly rotate App Passwords
- Keep 2-Factor Authentication enabled
- Monitor for unauthorized access

## 📞 Support

If you encounter issues:
1. Check the troubleshooting section above
2. Verify all steps were completed correctly
3. Test with a different Gmail account
4. Check Laravel logs for detailed error messages

## 🚀 Advanced Features

### Custom Email Templates
- Edit: `resources/views/emails/sale-receipt.blade.php`
- Customize colors, branding, and layout
- Add company logo and contact information

### Multiple Email Addresses
- Configure additional mailers in `config/mail.php`
- Use different email addresses for different purposes

### Email Queue Processing
- Configure queue system for bulk email sending
- Improve performance for high-volume sales

---

**Your email receipt system is now ready! 🎉**
