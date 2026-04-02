<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EloadTransaction;
use Carbon\Carbon;

class CleanupOldEloadTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eload:cleanup-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete TV E-Load transactions older than 1 year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of old TV E-Load transactions...');
        
        // Calculate date 1 year ago
        $cutoffDate = Carbon::now()->subYear(1);
        
        // Count old transactions
        $oldTransactions = EloadTransaction::where('created_at', '<', $cutoffDate)
            ->where('status', 'completed')
            ->count();
            
        if ($oldTransactions === 0) {
            $this->info('No old transactions found. Cleanup complete.');
            return 0;
        }
        
        $this->info("Found {$oldTransactions} transactions older than 1 year.");
        
        // Delete old transactions
        $deletedCount = EloadTransaction::where('created_at', '<', $cutoffDate)
            ->where('status', 'completed')
            ->delete();
            
        $this->info("Successfully deleted {$deletedCount} old TV E-Load transactions.");
        $this->info('Cleanup completed successfully.');
        
        return $deletedCount;
    }
}
