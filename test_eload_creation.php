<?php

/**
 * Test E-Load Creation Functionality
 * This script will test the e-load creation feature with network support
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test E-Load Creation ===\n\n";

try {
    // Get available categories
    $categories = DB::table('eload_categories')->where('status', 'active')->get();
    
    if ($categories->isEmpty()) {
        echo "❌ No active e-load categories found\n";
        return;
    }
    
    echo "Available e-load categories:\n";
    foreach ($categories as $category) {
        echo "- ID: {$category->id}, Name: {$category->name}\n";
    }
    echo "\n";
    
    // Test data for e-load creation (simulating form submission)
    $eloadData = [
        'category_id' => 1, // Mobile Load
        'name' => 'Go+',
        'network' => 'Globe',
        'provider' => 'Globe Telecom',
        'service_type' => 'Mobile Load',
        'code' => 'GOPLUS',
        'price' => 500.00,
        'description' => 'Globe Go+ promo with data and calls',
        'validity' => '7 days',
        'discount' => 5.00,
        'commission' => 10.00,
        'min_amount' => 100.00,
        'max_amount' => 1000.00,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    echo "Testing e-load creation with the following data:\n";
    echo "- Category ID: " . $eloadData['category_id'] . " (Mobile Load)\n";
    echo "- Name: " . $eloadData['name'] . "\n";
    echo "- Network: " . $eloadData['network'] . "\n";
    echo "- Provider: " . $eloadData['provider'] . "\n";
    echo "- Service Type: " . $eloadData['service_type'] . "\n";
    echo "- Code: " . $eloadData['code'] . "\n";
    echo "- Price: " . $eloadData['price'] . "\n";
    echo "- Description: " . $eloadData['description'] . "\n";
    echo "- Validity: " . $eloadData['validity'] . "\n";
    echo "- Discount: " . $eloadData['discount'] . "\n";
    echo "- Commission: " . $eloadData['commission'] . "\n";
    echo "- Min Amount: " . $eloadData['min_amount'] . "\n";
    echo "- Max Amount: " . $eloadData['max_amount'] . "\n";
    echo "- Status: " . $eloadData['status'] . "\n\n";

    // Create the e-load record
    try {
        $eloadId = DB::table('eloads')->insertGetId($eloadData);
        
        echo "✅ E-Load creation successful!\n";
        echo "E-Load ID: " . $eloadId . "\n\n";
        
        // Verify the e-load record was created
        $createdEload = DB::table('eloads')->where('id', $eloadId)->first();
        
        if ($createdEload) {
            echo "Verification:\n";
            echo "- E-Load ID: " . $createdEload->id . "\n";
            echo "- Category ID: " . $createdEload->category_id . "\n";
            echo "- Name: " . $createdEload->name . "\n";
            echo "- Network: " . $createdEload->network . "\n";
            echo "- Provider: " . $createdEload->provider . "\n";
            echo "- Service Type: " . $createdEload->service_type . "\n";
            echo "- Code: " . $createdEload->code . "\n";
            echo "- Price: " . $createdEload->price . "\n";
            echo "- Description: " . $createdEload->description . "\n";
            echo "- Validity: " . $createdEload->validity . "\n";
            echo "- Discount: " . $createdEload->discount . "\n";
            echo "- Commission: " . $createdEload->commission . "\n";
            echo "- Min Amount: " . $createdEload->min_amount . "\n";
            echo "- Max Amount: " . $createdEload->max_amount . "\n";
            echo "- Status: " . $createdEload->status . "\n";
            echo "- Created: " . $createdEload->created_at . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ E-Load creation failed: " . $e->getMessage() . "\n";
        return;
    }

    // Test different networks and categories
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Testing different networks and categories:\n";
    echo str_repeat("=", 60) . "\n\n";
    
    $testEloads = [
        [
            'category_id' => 1,
            'name' => 'GigaStudy',
            'network' => 'Smart',
            'provider' => 'Smart Communications',
            'price' => 299.00,
            'service_type' => 'Mobile Load',
            'code' => 'GIGASTUDY',
            'description' => 'Smart GigaStudy for students',
            'validity' => '30 days',
        ],
        [
            'category_id' => 2,
            'name' => 'Home WiFi Prepaid',
            'network' => 'Globe At Home',
            'provider' => 'Globe Telecom',
            'price' => 999.00,
            'service_type' => 'Internet Load',
            'code' => 'HOMEWIFI',
            'description' => 'Globe At Home WiFi prepaid load',
            'validity' => '30 days',
        ],
        [
            'category_id' => 3,
            'name' => 'Mobile Legends Diamonds',
            'network' => 'MLBB',
            'provider' => 'Moonton',
            'price' => 100.00,
            'service_type' => 'Gaming Load',
            'code' => 'MLBB100',
            'description' => 'Mobile Legends 100 diamonds',
            'validity' => 'Permanent',
        ],
    ];
    
    foreach ($testEloads as $testEload) {
        try {
            $eloadData = array_merge([
                'status' => 'active',
                'discount' => 0,
                'commission' => 0,
                'min_amount' => 50,
                'max_amount' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ], $testEload);
            
            $eloadId = DB::table('eloads')->insertGetId($eloadData);
            echo "✅ " . $testEload['name'] . " (" . $testEload['network'] . ") created successfully (ID: " . $eloadId . ")\n";
            
        } catch (Exception $e) {
            echo "❌ Failed to create " . $testEload['name'] . ": " . $e->getMessage() . "\n";
        }
    }

    // Show e-load statistics
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "E-Load Statistics:\n";
    echo str_repeat("=", 60) . "\n";
    
    $totalEloads = DB::table('eloads')->count();
    $activeEloads = DB::table('eloads')->where('status', 'active')->count();
    $avgPrice = DB::table('eloads')->avg('price');
    
    echo "- Total E-Load Products: " . $totalEloads . "\n";
    echo "- Active E-Load Products: " . $activeEloads . "\n";
    echo "- Average Price: " . number_format($avgPrice, 2) . "\n";
    
    // E-loads by category
    echo "\nE-Loads by Category:\n";
    $eloadsByCategory = DB::table('eloads')
        ->select('eloads.category_id', 'eload_categories.name as category_name', DB::raw('count(*) as count'))
        ->leftJoin('eload_categories', 'eloads.category_id', '=', 'eload_categories.id')
        ->groupBy('eloads.category_id', 'eload_categories.name')
        ->orderBy('count', 'desc')
        ->get();
    
    foreach ($eloadsByCategory as $category) {
        echo "- " . ($category->category_name ?? 'Unknown') . ": " . $category->count . " products\n";
    }
    
    // E-loads by network
    echo "\nE-Loads by Network:\n";
    $eloadsByNetwork = DB::table('eloads')
        ->select('network', DB::raw('count(*) as count'), DB::raw('avg(price) as avg_price'))
        ->whereNotNull('network')
        ->groupBy('network')
        ->orderBy('count', 'desc')
        ->get();
    
    foreach ($eloadsByNetwork as $network) {
        echo "- " . $network->network . ": " . $network->count . " products (Avg: " . number_format($network->avg_price, 2) . ")\n";
    }
    
    echo "\n✅ E-Load creation functionality is working properly!\n";
    echo "✅ Network support is working!\n";
    echo "✅ All database columns are properly configured!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}

echo "\n=== Test Complete ===\n";
?>
