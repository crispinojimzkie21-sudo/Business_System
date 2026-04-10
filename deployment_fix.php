<?php

/**
 * Production Deployment Fix Script
 * Run this script on your server to fix common deployment issues
 */

echo "=== RM Manliquid Business System - Deployment Fix ===\n\n";

// 1. Clear all caches
echo "1. Clearing caches...\n";
shell_exec('php artisan cache:clear');
shell_exec('php artisan config:clear');
shell_exec('php artisan route:clear');
shell_exec('php artisan view:clear');
echo "✓ Caches cleared\n\n";

// 2. Set proper permissions
echo "2. Setting proper permissions...\n";
shell_exec('chmod -R 755 storage');
shell_exec('chmod -R 755 bootstrap/cache');
shell_exec('chmod -R 755 public');
echo "✓ Permissions set\n\n";

// 3. Optimize for production
echo "3. Optimizing for production...\n";
shell_exec('php artisan config:cache');
shell_exec('php artisan route:cache');
shell_exec('php artisan view:cache');
echo "✓ Application optimized\n\n";

// 4. Check if assets are built
echo "4. Checking build assets...\n";
if (file_exists('public/build/manifest.json')) {
    echo "✓ Build assets found\n";
} else {
    echo "⚠ Build assets not found. Run 'npm run build' first.\n";
}

// 5. Check favicon
echo "5. Checking favicon...\n";
if (file_exists('public/favicon.svg')) {
    echo "✓ Favicon found\n";
} else {
    echo "⚠ Favicon not found\n";
}

// 6. Check database connection
echo "6. Checking database configuration...\n";
try {
    if (file_exists('database/database.sqlite')) {
        $pdo = new PDO('sqlite:database/database.sqlite');
        echo "✓ Database connection successful\n";
    } else {
        echo "⚠ SQLite database not found. Create database/database.sqlite or configure MySQL.\n";
    }
} catch (Exception $e) {
    echo "⚠ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== Deployment Fix Complete ===\n";
echo "Next steps:\n";
echo "1. Update your .env file with production values\n";
echo "2. Set APP_DEBUG=false\n";
echo "3. Set APP_URL to your domain\n";
echo "4. Configure your web server (Apache/Nginx)\n";
echo "5. Test your application\n";

?>
