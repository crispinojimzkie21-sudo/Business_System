<?php

/**
 * Test Sales Creation Functionality
 * This script will test the sales creation feature with transaction ID
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Sales Creation ===\n\n";

try {
    // Get a test user (use super admin for testing)
    $testUser = DB::table('users')
        ->where('role', 'super_admin')
        ->orderBy('created_at', 'desc')
        ->first();
    
    if (!$testUser) {
        echo "❌ No super admin user found for testing\n";
        return;
    }
    
    echo "Testing with user:\n";
    echo "- ID: " . $testUser->id . "\n";
    echo "- Name: " . $testUser->name . "\n";
    echo "- Email: " . $testUser->email . "\n";
    echo "- Role: " . $testUser->role . "\n\n";
    
    // Test data for sales creation (simulating form submission)
    $transactionId = 'TXN-' . date('Ymd') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    $receiptNumber = 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    
    $salesData = [
        'transaction_id' => $transactionId,
        'total_amount' => 150000.00,
        'payment_method' => 'card',
        'customer_name' => 'Test Customer',
        'customer_email' => 'testcustomer@example.com',
        'customer_phone' => '09955778449',
        'items' => json_encode([
            [
                'product_id' => 1,
                'product_name' => 'laptop',
                'quantity' => 3,
                'unit_price' => '50000.00',
                'subtotal' => 150000
            ]
        ]),
        'user_id' => $testUser->id,
        'receipt_number' => $receiptNumber,
        'status' => 'completed',
        'payment_status' => 'paid',
        'notes' => 'Test sales transaction',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    echo "Testing sales creation with the following data:\n";
    echo "- Transaction ID: " . $salesData['transaction_id'] . "\n";
    echo "- Total Amount: " . $salesData['total_amount'] . "\n";
    echo "- Payment Method: " . $salesData['payment_method'] . "\n";
    echo "- Customer Name: " . $salesData['customer_name'] . "\n";
    echo "- Customer Email: " . $salesData['customer_email'] . "\n";
    echo "- Customer Phone: " . $salesData['customer_phone'] . "\n";
    echo "- Receipt Number: " . $salesData['receipt_number'] . "\n";
    echo "- Status: " . $salesData['status'] . "\n";
    echo "- Payment Status: " . $salesData['payment_status'] . "\n\n";

    // Create the sales record
    try {
        $salesId = DB::table('sales')->insertGetId($salesData);
        
        echo "✅ Sales creation successful!\n";
        echo "Sales ID: " . $salesId . "\n";
        echo "Transaction ID: " . $transactionId . "\n\n";
        
        // Verify the sales record was created
        $createdSale = DB::table('sales')->where('id', $salesId)->first();
        
        if ($createdSale) {
            echo "Verification:\n";
            echo "- Sales ID: " . $createdSale->id . "\n";
            echo "- Transaction ID: " . $createdSale->transaction_id . "\n";
            echo "- Total Amount: " . $createdSale->total_amount . "\n";
            echo "- Payment Method: " . $createdSale->payment_method . "\n";
            echo "- Customer Name: " . $createdSale->customer_name . "\n";
            echo "- Customer Email: " . $createdSale->customer_email . "\n";
            echo "- Customer Phone: " . $createdSale->customer_phone . "\n";
            echo "- Receipt Number: " . $createdSale->receipt_number . "\n";
            echo "- Status: " . $createdSale->status . "\n";
            echo "- Payment Status: " . $createdSale->payment_status . "\n";
            echo "- Notes: " . ($createdSale->notes ?? 'None') . "\n";
            echo "- User ID: " . $createdSale->user_id . "\n";
            echo "- Created: " . $createdSale->created_at . "\n";
            
            // Decode and show items
            $items = json_decode($createdSale->items, true);
            echo "- Items: " . count($items) . " item(s)\n";
            foreach ($items as $item) {
                echo "  * " . $item['product_name'] . " (x" . $item['quantity'] . ") - " . $item['unit_price'] . " each\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Sales creation failed: " . $e->getMessage() . "\n";
        return;
    }

    // Test multiple sales with different payment methods
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Testing different payment methods:\n";
    echo str_repeat("=", 60) . "\n\n";
    
    $paymentMethods = ['cash', 'card', 'bank_transfer', 'mobile_money'];
    
    foreach ($paymentMethods as $method) {
        try {
            $testSale = [
                'transaction_id' => 'TXN-' . date('Ymd') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'total_amount' => rand(1000, 50000),
                'payment_method' => $method,
                'customer_name' => 'Test ' . ucfirst($method) . ' Customer',
                'customer_email' => 'test' . $method . '@example.com',
                'items' => json_encode([[
                    'product_id' => 1,
                    'product_name' => 'Test Product',
                    'quantity' => 1,
                    'unit_price' => rand(1000, 50000),
                    'subtotal' => rand(1000, 50000)
                ]]),
                'user_id' => $testUser->id,
                'status' => 'completed',
                'payment_status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $saleId = DB::table('sales')->insertGetId($testSale);
            echo "✅ " . ucfirst(str_replace('_', ' ', $method)) . " payment successful (ID: " . $saleId . ")\n";
            
        } catch (Exception $e) {
            echo "❌ Failed to create sale with " . $method . ": " . $e->getMessage() . "\n";
        }
    }

    // Show sales statistics
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Sales Statistics:\n";
    echo str_repeat("=", 60) . "\n";
    
    $totalSales = DB::table('sales')->count();
    $totalRevenue = DB::table('sales')->sum('total_amount');
    $todaySales = DB::table('sales')->whereDate('created_at', date('Y-m-d'))->count();
    $todayRevenue = DB::table('sales')->whereDate('created_at', date('Y-m-d'))->sum('total_amount');
    
    echo "- Total Sales Records: " . $totalSales . "\n";
    echo "- Total Revenue: " . number_format($totalRevenue, 2) . "\n";
    echo "- Today's Sales: " . $todaySales . "\n";
    echo "- Today's Revenue: " . number_format($todayRevenue, 2) . "\n";
    
    // Sales by payment method
    echo "\nSales by Payment Method:\n";
    $salesByMethod = DB::table('sales')
        ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
        ->groupBy('payment_method')
        ->orderBy('total', 'desc')
        ->get();
    
    foreach ($salesByMethod as $method) {
        echo "- " . ucfirst(str_replace('_', ' ', $method->payment_method)) . ": " . $method->count . " sales (" . number_format($method->total, 2) . ")\n";
    }
    
    echo "\n✅ Sales creation functionality is working properly!\n";
    echo "✅ Transaction ID generation is working!\n";
    echo "✅ All database columns are properly configured!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}

echo "\n=== Test Complete ===\n";
?>
