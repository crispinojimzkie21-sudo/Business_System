<?php

/**
 * Enable Gmail Account
 * This script will enable the Just@gmail.com account
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Enable Gmail Account ===\n\n";

try {
    echo "Enabling Just@gmail.com account...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Find the Gmail account
    $gmailUser = DB::table('users')
        ->where('email', 'Just@gmail.com')
        ->first();
    
    if ($gmailUser) {
        echo "Found Gmail account:\n";
        echo "- ID: {$gmailUser->id}\n";
        echo "- Name: {$gmailUser->name}\n";
        echo "- Email: {$gmailUser->email}\n";
        echo "- Current Status: " . ($gmailUser->access_enabled ? 'Enabled' : 'Disabled') . "\n";
        
        // Enable the account
        $updated = DB::table('users')
            ->where('id', $gmailUser->id)
            ->update([
                'access_enabled' => 1,
                'updated_at' => now(),
            ]);
        
        if ($updated) {
            echo "\nSUCCESS: Gmail account has been enabled!\n";
            
            // Verify the change
            $updatedUser = DB::table('users')->where('id', $gmailUser->id)->first();
            echo "- New Status: " . ($updatedUser->access_enabled ? 'Enabled' : 'Disabled') . "\n";
            echo "- Updated At: {$updatedUser->updated_at}\n";
            
            echo "\nLogin Information:\n";
            echo "URL: http://127.0.0.1:8000/login\n";
            echo "Email: Just@gmail.com\n";
            echo "Password: (Use your existing password or contact admin for reset)\n";
        } else {
            echo "\nERROR: Failed to enable Gmail account\n";
        }
    } else {
        echo "ERROR: Gmail account Just@gmail.com not found in system\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Enable Complete ===\n";
?>
