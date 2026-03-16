<?php
/**
 * Fix Timezone to Philippine Time (Asia/Manila)
 */

$configFile = __DIR__ . '/config/app.php';

if (file_exists($configFile)) {
    $content = file_get_contents($configFile);
    
    // Replace UTC with Asia/Manila
    $newContent = str_replace(
        "'timezone' => 'UTC'",
        "'timezone' => 'Asia/Manila'",
        $content
    );
    
    file_put_contents($configFile, $newContent);
    echo "Timezone updated to Asia/Manila (Philippine Time)\n";
} else {
    echo "config/app.php not found\n";
}

