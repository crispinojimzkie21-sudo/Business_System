<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Eload;
use App\Models\EloadTransaction;
use App\Models\Attendance;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class ClearAllData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all-data {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data from products, e-load, and attendance records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('This will permanently delete ALL data from:');
            $this->line('1. Products table');
            $this->line('2. E-load products table');
            $this->line('3. E-load transactions table');
            $this->line('4. Attendance records table');
            $this->line('5. Sales records table');
            
            if (!$this->confirm('Do you want to continue? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting data cleanup...');

        try {
            // For SQLite, we need to disable foreign keys differently
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } else {
                // For MySQL/MariaDB
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            // Clear Sales records first (depends on products)
            $salesCount = Sale::count();
            Sale::truncate();
            $this->info("✅ Cleared {$salesCount} sales records");

            // Clear Attendance records
            $attendanceCount = Attendance::count();
            Attendance::truncate();
            $this->info("✅ Cleared {$attendanceCount} attendance records");

            // Clear E-load Transactions
            $eloadTransactionCount = EloadTransaction::count();
            EloadTransaction::truncate();
            $this->info("✅ Cleared {$eloadTransactionCount} e-load transactions");

            // Clear E-load Products
            $eloadCount = Eload::count();
            Eload::truncate();
            $this->info("✅ Cleared {$eloadCount} e-load products");

            // Clear Products
            $productCount = Product::count();
            Product::truncate();
            $this->info("✅ Cleared {$productCount} products");

            // Re-enable foreign key checks
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            $this->newLine();
            $this->info('🎉 All data has been successfully cleared!');
            $this->info('Summary of deleted records:');
            $this->line("- Products: {$productCount}");
            $this->line("- E-load Products: {$eloadCount}");
            $this->line("- E-load Transactions: {$eloadTransactionCount}");
            $this->line("- Attendance Records: {$attendanceCount}");
            $this->line("- Sales Records: {$salesCount}");

        } catch (\Exception $e) {
            $this->error('❌ Error occurred while clearing data: ' . $e->getMessage());
            
            // Re-enable foreign key checks in case of error
            try {
                if (DB::getDriverName() === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                }
            } catch (\Exception $e2) {
                $this->error('Could not re-enable foreign key checks: ' . $e2->getMessage());
            }
            
            return 1;
        }

        return 0;
    }
}
