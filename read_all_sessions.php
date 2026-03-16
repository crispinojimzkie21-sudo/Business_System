<?php
$sessionDir = __DIR__ . '/storage/framework/sessions/';
$files = glob($sessionDir . '*');

echo "=== Session Files ===\n\n";

foreach ($files as $file) {
    $filename = basename($file);
    $content = file_get_contents($file);
    echo "File: $filename\n";
    echo "Size: " . strlen($content) . " bytes\n";
    echo "Content:\n$content\n\n";
    echo "---\n\n";
}
