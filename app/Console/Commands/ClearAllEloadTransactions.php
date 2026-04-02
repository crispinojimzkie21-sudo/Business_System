<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EloadTransaction;

class ClearAllEloadTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eload:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all TV E-Load transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of all TV E-Load transactions...');
        
        // Count all transactions
        $totalTransactions = EloadTransaction::where('status', 'completed')->count();
        
        if ($totalTransactions === 0) {
            $this->info('No TV E-Load transactions found.');
            return 0;
        }
        
        $this->info("Found {$totalTransactions} TV E-Load transactions.");
        
        // Confirm deletion
        if (!$this->confirm('Are you sure you want to delete ALL TV E-Load transactions? This action cannot be undone.')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }
        
        // Delete all transactions
        $deletedCount = EloadTransaction::where('status', 'completed')->delete();
        
        $this->info("Successfully deleted {$deletedCount} TV E-Load transactions.");
        $this->info('All TV E-Load transactions have been cleared.');
        
        return $deletedCount;
    }
}
