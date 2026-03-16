<?php
$sessionFile = $argv[1] ?? '';
if (!$sessionFile) {
    echo "Usage: php read_session.php <filename>\n";
    exit(1);
}

$path = __DIR__ . '/storage/framework/sessions/' . $sessionFile;
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(1);
}

$content = file_get_contents($path);
echo "Session file: $sessionFile\n";
echo "Size: " . strlen($content) . " bytes\n";
echo "Content:\n$content\n";
