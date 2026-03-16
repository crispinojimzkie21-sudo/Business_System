<?php
// Test MySQL connection
$ports = [3306, 3307, 3308];

foreach ($ports as $port) {
    echo "Testing port $port...\n";
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=$port", "root", "");
        echo "✅ MySQL accessible on port $port\n";
        
        // List databases
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Available databases: " . implode(", ", $databases) . "\n";
        
        // Check if business_system exists
        if (in_array('business_system', $databases)) {
            echo "✅ Database 'business_system' exists!\n";
        } else {
            echo "❌ Database 'business_system' does NOT exist\n";
        }
        break;
    } catch (Exception $e) {
        echo "❌ Port $port failed: " . $e->getMessage() . "\n";
    }
}

