<?php
/**
 * Cache Clearing Script for Laravel Application
 * Clears all types of cache: config, routes, views, application cache, and sessions
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "===========================================\n";
echo "    LARAVEL CACHE CLEARING SCRIPT\n";
echo "===========================================\n\n";

$basePath = __DIR__;

// 1. Clear Config Cache
echo "[1] Clearing Config Cache...\n";
$configCacheFiles = [
    $basePath . '/bootstrap/cache/config.php',
    $basePath . '/bootstrap/cache/packages.php',
    $basePath . '/bootstrap/cache/services.php',
];
foreach ($configCacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "    - Deleted: " . basename($file) . "\n";
    }
}
echo "    Config cache cleared!\n\n";

// 2. Clear Routes Cache
echo "[2] Clearing Routes Cache...\n";
$routesCache = $basePath . '/bootstrap/cache/routes.php';
if (file_exists($routesCache)) {
    unlink($routesCache);
    echo "    - Deleted: routes.php\n";
}
echo "    Routes cache cleared!\n\n";

// 3. Clear View Cache
echo "[3] Clearing View Cache...\n";
$viewsPath = $basePath . '/storage/framework/views';
if (is_dir($viewsPath)) {
    $viewFiles = glob($viewsPath . '/*');
    foreach ($viewFiles as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    // Remove cached directories
    $viewDirs = glob($viewsPath . '/**', GLOB_ONLYDIR);
    foreach ($viewDirs as $dir) {
        @rmdir($dir);
    }
    echo "    - Cleared files in: storage/framework/views\n";
}
echo "    View cache cleared!\n\n";

// 4. Clear Application Cache
echo "[4] Clearing Application Cache...\n";
$cachePath = $basePath . '/storage/framework/cache';
if (is_dir($cachePath)) {
    $cacheFiles = glob($cachePath . '/**/*');
    foreach ($cacheFiles as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "    - Cleared files in: storage/framework/cache\n";
}
echo "    Application cache cleared!\n\n";

// 5. Clear Session Files
echo "[5] Clearing Session Files...\n";
$sessionsPath = $basePath . '/storage/framework/sessions';
if (is_dir($sessionsPath)) {
    $sessionFiles = glob($sessionsPath . '/*');
    foreach ($sessionFiles as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "    - Cleared files in: storage/framework/sessions\n";
}
echo "    Session files cleared!\n\n";

// 6. Clear Database Sessions
echo "[6] Clearing Database Sessions...\n";
try {
    $sessionTable = config('session.table', 'sessions');
    if (DB::getSchemaBuilder()->hasTable($sessionTable)) {
        DB::table($sessionTable)->delete();
        echo "    - Cleared database table: $sessionTable\n";
    }
} catch (\Exception $e) {
    echo "    - Warning: Could not clear database sessions: " . $e->getMessage() . "\n";
}
echo "    Database sessions cleared!\n\n";

echo "===========================================\n";
echo "    ✅ ALL CACHES CLEARED SUCCESSFULLY!\n";
echo "===========================================\n";

