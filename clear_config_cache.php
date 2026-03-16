<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Clear config cache
$configPath = __DIR__ . '/bootstrap/cache/config.php';
if (file_exists($configPath)) {
    unlink($configPath);
    echo "Config cache cleared!\n";
} else {
    echo "No config cache to clear.\n";
}

// Clear routes cache
$routesPath = __DIR__ . '/bootstrap/cache/routes.php';
if (file_exists($routesPath)) {
    unlink($routesPath);
    echo "Routes cache cleared!\n";
}

echo "Done!\n";
$kernel->bootstrap();