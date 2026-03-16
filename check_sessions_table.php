<?php
$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    echo "❌ Database file not found: $dbPath\n";
    exit(1);
}

$db = new SQLite3($dbPath);

$result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='sessions'");

if ($result->fetchArray()) {
    echo "✅ Sessions table EXISTS in database\n";
    
    // Show session count
    $count = $db->query("SELECT COUNT(*) as cnt FROM sessions");
    $row = $count->fetchArray(SQLITE3_ASSOC);
    echo "📊 Current sessions in DB: " . $row['cnt'] . "\n";
} else {
    echo "❌ Sessions table NOT found\n";
    echo "Run: php artisan session:table then php artisan migrate\n";
}

