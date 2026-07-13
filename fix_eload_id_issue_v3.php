<?php

/**
 * Fix E-Load ID Issue V3
 * This script will fix the eload_id NOT NULL constraint issue
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix E-Load ID Issue V3 ===\n\n";

try {
    echo "Checking and fixing eload_id NOT NULL constraint issue...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Check current EloadController processLoad method
    echo "1. Checking current EloadController processLoad method...\n";
    
    $controllerFile = 'c:\xampp\htdocs\Business_System\app\Http\Controllers\EloadController.php';
    $controllerContent = file_get_contents($controllerFile);
    
    // Check if the fix is already applied
    if (strpos($controllerContent, "'eload_id' => \$eload->id") !== false) {
        echo "SUCCESS: eload_id fix is present in controller\n";
    } else {
        echo "ERROR: eload_id fix is missing from controller\n";
        echo "Applying fix now...\n";
        
        // Find and fix the processLoad method
        $oldPattern = '/\$eloadNumberRecord = EloadNumber::create\(\[\s*\n\s*\'number\' => \$eloadNumber,\s*\n\s*\'network\' => \$request->network,/s';
        $newReplacement = '$eloadNumberRecord = EloadNumber::create([
                \'eload_id\' => $eload->id,
                \'number\' => $eloadNumber,
                \'network\' => $request->network,';
        
        $updatedContent = preg_replace($oldPattern, $newReplacement, $controllerContent);
        
        if ($updatedContent && $updatedContent !== $controllerContent) {
            file_put_contents($controllerFile, $updatedContent);
            echo "SUCCESS: Controller updated with eload_id fix\n";
        } else {
            echo "ERROR: Failed to update controller\n";
        }
    }
    
    // 2. Test the actual processLoad method
    echo "\n2. Testing the processLoad method...\n";
    
    $controller = new \App\Http\Controllers\EloadController();
    
    $mockRequest = new \Illuminate\Http\Request();
    $mockRequest->merge([
        'network' => 'Gomo',
        'eload_number' => '09955778123',
        'price' => 100,
        'status' => 'completed',
    ]);
    
    // Mock Auth
    if (!class_exists('Illuminate\Support\Facades\Auth')) {
        class MockAuth {
            public static function id() { return 1; }
            public static function user() { 
                $user = new stdClass();
                $user->id = 1;
                return $user;
            }
        }
        class_alias('MockAuth', 'Illuminate\Support\Facades\Auth');
    }
    
    try {
        echo "Testing processLoad method execution...\n";
        $response = $controller->processLoad($mockRequest);
        echo "SUCCESS: processLoad method executed successfully\n";
        
        // Check if the transaction was created
        $latestTransaction = DB::table('eload_transactions')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestTransaction) {
            echo "Latest transaction details:\n";
            echo "- Transaction ID: {$latestTransaction->transaction_id}\n";
            echo "- E-Load Number: {$latestTransaction->eload_number}\n";
            echo "- Network: {$latestTransaction->network}\n";
            echo "- Price: {$latestTransaction->price}\n";
            echo "- Status: {$latestTransaction->status}\n";
            
            // Check if the eload_number was created correctly
            $eloadNumberRecord = DB::table('eload_numbers')
                ->where('number', '09955778123')
                ->first();
            
            if ($eloadNumberRecord) {
                echo "E-Load Number record:\n";
                echo "- ID: {$eloadNumberRecord->id}\n";
                echo "- E-Load ID: " . ($eloadNumberRecord->eload_id ?? 'NULL') . "\n";
                echo "- Number: {$eloadNumberRecord->number}\n";
                echo "- Network: {$eloadNumberRecord->network}\n";
                echo "- Status: {$eloadNumberRecord->status}\n";
                
                if ($eloadNumberRecord->eload_id) {
                    echo "SUCCESS: eload_id is properly set\n";
                } else {
                    echo "ERROR: eload_id is still NULL\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "ERROR: processLoad method failed - " . $e->getMessage() . "\n";
        
        // Check if it's the eload_id constraint error
        if (strpos($e->getMessage(), 'eload_id') !== false) {
            echo "This is the eload_id constraint error we're trying to fix\n";
            
            // Let's manually create a test to see what's happening
            echo "\n3. Manual test of eload_number creation...\n";
            
            try {
                // Create a test eload first
                $category = DB::table('eload_categories')->first();
                if (!$category) {
                    $categoryId = DB::table('eload_categories')->insertGetId([
                        'name' => 'Default',
                        'description' => 'Default category',
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $category = DB::table('eload_categories')->where('id', $categoryId)->first();
                }
                
                $eloadId = DB::table('eloads')->insertGetId([
                    'name' => 'Test Load - Gomo',
                    'network' => 'Gomo',
                    'provider' => 'Gomo Telecom',
                    'price' => 100,
                    'category_id' => $category->id,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "Created test eload with ID: {$eloadId}\n";
                
                // Now try to create eload_number with eload_id
                $eloadNumberId = DB::table('eload_numbers')->insertGetId([
                    'eload_id' => $eloadId,
                    'number' => '09955778123',
                    'network' => 'Gomo',
                    'provider' => 'Gomo Telecom',
                    'number_type' => 'Mobile',
                    'description' => 'Gomo mobile number',
                    'is_active' => 1,
                    'priority' => 1,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "SUCCESS: eload_number created with ID: {$eloadNumberId}\n";
                echo "The issue might be in the controller logic, not the database\n";
                
            } catch (Exception $manualError) {
                echo "Manual test also failed: " . $manualError->getMessage() . "\n";
            }
        }
    }
    
    // 4. Check database schema
    echo "\n4. Checking database schema...\n";
    
    $columns = DB::select("PRAGMA table_info(eload_numbers)");
    
    $hasEloadId = false;
    $eloadIdNullable = false;
    
    foreach ($columns as $column) {
        if ($column->name === 'eload_id') {
            $hasEloadId = true;
            $eloadIdNullable = !$column->notnull;
            break;
        }
    }
    
    echo "eload_id column exists: " . ($hasEloadId ? 'YES' : 'NO') . "\n";
    echo "eload_id is nullable: " . ($eloadIdNullable ? 'YES' : 'NO') . "\n";
    
    if ($hasEloadId && !$eloadIdNullable) {
        echo "SUCCESS: eload_id column is properly configured as NOT NULL\n";
    } else {
        echo "ERROR: eload_id column configuration issue\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Fix Complete ===\n";
?>
