<?php
// Mark the sessions migration as completed since table already exists
$dbPath = __DIR__ . '/database/database.sqlite';

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if migrations table exists
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
    if ($stmt->fetch()) {
        // Check if sessions migration already recorded
        $stmt = $pdo->query("SELECT * FROM migrations WHERE migration = '2026_03_03_044230_create_sessions_table'");
        if (!$stmt->fetch()) {
            $pdo->exec("INSERT INTO migrations (migration, batch) VALUES ('2026_03_03_044230_create_sessions_table', 1)");
            echo "✅ Marked sessions migration as completed\n";
        } else {
            echo "ℹ️  Sessions migration already marked as completed\n";
        }
        
        // Check if contact_submissions migration already recorded
        $stmt = $pdo->query("SELECT * FROM migrations WHERE migration = '2026_03_03_081055_create_contact_submissions_table'");
        if (!$stmt->fetch()) {
            $pdo->exec("INSERT INTO migrations (migration, batch) VALUES ('2026_03_03_081055_create_contact_submissions_table', 1)");
            echo "✅ Marked contact_submissions migration as completed\n";
        } else {
            echo "ℹ️  Contact submissions migration already marked as completed\n";
        }
    }
    
    // Check users table
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM users");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    echo "ℹ️  Users in database: $count\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

