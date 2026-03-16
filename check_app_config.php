<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "APP_KEY: " . config('app.key') . "\n";
echo "SESSION_ENCRYPT: " . (config('session.encrypt') ? 'true' : 'false') . "\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";

