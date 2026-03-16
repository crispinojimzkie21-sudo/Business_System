<?php

/**
 * Update Gmail SMTP Configuration for Manliquid Store
 */

echo "=== Updating Gmail SMTP for Manliquid Store ===\n\n";

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

// Add new Gmail SMTP config for Manliquid Store
$mailConfig = [
    'MAIL_MAILER=smtp',
    'MAIL_HOST=smtp.gmail.com',
    'MAIL_PORT=587',
    'MAIL_USERNAME=manliquidstore@gmail.com',
    'MAIL_PASSWORD=bbbdkcfxzcfvprva',
    'MAIL_ENCRYPTION=tls',
    'MAIL_FROM_ADDRESS=manliquidstore@gmail.com',
    'MAIL_FROM_NAME="Manliquid Store"'
];

$envContent = implode("\n", $newLines) . "\n" . implode("\n", $mailConfig);

if (file_put_contents($envFile, $envContent)) {
    echo "✅ Gmail SMTP configuration updated successfully!\n\n";
    
    echo "📋 Configuration Added:\n";
    foreach ($mailConfig as $config) {
        echo "  $config\n";
    }
    
    echo "\n🧪 Testing configuration...\n";
    
    // Test the configuration
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    try {
        \Illuminate\Support\Facades\Mail::raw('Test Email from Manliquid Store', function($message) {
            $message->to('manliquidstore@gmail.com')
                    ->subject('Gmail SMTP Test - ' . date('Y-m-d H:i:s'));
        });
        
        echo "✅ SUCCESS! Test email sent to manliquidstore@gmail.com\n";
        echo "Check your inbox (and spam folder)\n\n";
        
        echo "🎉 Email receipts are now configured and working!\n";
        echo "Customers will receive receipts when sales are made with email addresses.\n";
        
    } catch (Exception $e) {
        echo "❌ FAILED! Error: " . $e->getMessage() . "\n";
        echo "Please check your Gmail account settings.\n";
    }
    
    echo "\n🔄 Clearing caches...\n";
    shell_exec('php artisan config:clear');
    echo "✅ Configuration cache cleared\n";
    
} else {
    echo "❌ Failed to update .env file!\n";
}
