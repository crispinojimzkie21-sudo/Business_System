<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Eload;
use App\Models\EloadTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClearUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:user-data {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all user data (attendance, sales, transactions) while keeping system structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('This will permanently delete ALL user data:');
            $this->line('1. Attendance records (all check-ins/check-outs)');
            $this->line('2. Sales transactions');
            $this->line('3. E-load transactions');
            $this->line('4. Product inventory (will reset to zero)');
            $this->line('5. E-load products');
            $this->warn('NOTE: User accounts and system settings will be preserved');
            
            if (!$this->confirm('Do you want to continue? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting user data cleanup...');

        try {
            // For SQLite, we need to disable foreign keys differently
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } else {
                // For MySQL/MariaDB
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            // Clear Attendance records
            $attendanceCount = Attendance::count();
            Attendance::truncate();
            $this->info("✅ Cleared {$attendanceCount} attendance records");

            // Clear Sales records
            $salesCount = Sale::count();
            Sale::truncate();
            $this->info("✅ Cleared {$salesCount} sales records");

            // Clear E-load Transactions
            $eloadTransactionCount = EloadTransaction::count();
            EloadTransaction::truncate();
            $this->info("✅ Cleared {$eloadTransactionCount} e-load transactions");

            // Clear E-load Products
            $eloadCount = Eload::count();
            Eload::truncate();
            $this->info("✅ Cleared {$eloadCount} e-load products");

            // Reset Products (keep structure but reset inventory)
            $productCount = Product::count();
            Product::query()->update([
                'stock_quantity' => 0,
                'min_stock_level' => 5,
                'price' => 0,
                'cost' => 0,
                'description' => 'Product reset - please update details',
                'updated_at' => now()
            ]);
            $this->info("✅ Reset {$productCount} products to zero inventory");

            // Re-enable foreign key checks
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            $this->newLine();
            $this->info('🎉 All user data has been successfully cleared!');
            $this->info('Summary of cleared data:');
            $this->line("- Attendance Records: {$attendanceCount}");
            $this->line("- Sales Records: {$salesCount}");
            $this->line("- E-load Transactions: {$eloadTransactionCount}");
            $this->line("- E-load Products: {$eloadCount}");
            $this->line("- Products Reset: {$productCount}");
            $this->newLine();
            $this->info('✅ User accounts and system settings preserved');
            $this->info('✅ System structure maintained');
            $this->info('✅ Ready for fresh data entry');

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
