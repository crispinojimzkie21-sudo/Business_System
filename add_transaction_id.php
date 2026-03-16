<?php
/**
 * Add transaction_id column to sales table
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding transaction_id column to sales table...\n";

try {
    // Check if column already exists
    $columns = DB::getSchemaBuilder()->getColumnListing('sales');
    
    if (!in_array('transaction_id', $columns)) {
        DB::statement('ALTER TABLE sales ADD COLUMN transaction_id VARCHAR(255) NULL');
        echo "✅ Column 'transaction_id' added successfully!\n";
    } else {
        echo "ℹ️ Column 'transaction_id' already exists.\n";
    }
    
    // Generate transaction IDs for existing records that don't have one
    $sales = DB::table('sales')->whereNull('transaction_id')->get();
    
    if ($sales->count() > 0) {
        echo "Generating transaction IDs for {$sales->count()} existing records...\n";
        
        foreach ($sales as $sale) {
            $transactionId = 'TXN-' . date('Ymd') . '-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
            DB::table('sales')->where('id', $sale->id)->update(['transaction_id' => $transactionId]);
        }
        
        echo "✅ Transaction IDs generated successfully!\n";
    } else {
        echo "ℹ️ All sales records already have transaction IDs.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";

