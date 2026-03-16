<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--auto : Automatic backup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database and important files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('auto')) {
            $this->info('Starting backup process...');
            $this->warn('This will backup:');
            $this->line('1. Database (SQLite)');
            $this->line('2. Uploaded files (if any)');
            
            if (!$this->confirm('Do you want to continue?')) {
                $this->info('Backup cancelled.');
                return 0;
            }
        }

        try {
            $backupPath = storage_path('backups');
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            
            // Create backup directory if it doesn't exist
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Backup database
            $this->info('Backing up database...');
            $databasePath = database_path('database.sqlite');
            $backupDatabasePath = $backupPath . '/database_' . $timestamp . '.sqlite';
            
            if (File::exists($databasePath)) {
                File::copy($databasePath, $backupDatabasePath);
                $this->info('✅ Database backed up successfully');
            } else {
                $this->warn('⚠️ Database file not found');
            }

            // Backup uploaded files (if they exist)
            $uploadsPath = public_path('uploads');
            if (File::exists($uploadsPath)) {
                $this->info('Backing up uploaded files...');
                $backupUploadsPath = $backupPath . '/uploads_' . $timestamp;
                File::copyDirectory($uploadsPath, $backupUploadsPath);
                $this->info('✅ Uploaded files backed up successfully');
            }

            // Create backup info file
            $backupInfo = [
                'timestamp' => $timestamp,
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'database_backup' => $backupDatabasePath,
                'uploads_backup' => isset($backupUploadsPath) ? $backupUploadsPath : null,
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
            ];

            $infoPath = $backupPath . '/backup_info_' . $timestamp . '.json';
            File::put($infoPath, json_encode($backupInfo, JSON_PRETTY_PRINT));

            // Clean old backups (keep only last 10)
            $this->cleanOldBackups($backupPath);

            $this->newLine();
            $this->info('🎉 Backup completed successfully!');
            $this->info('Backup location: ' . $backupPath);
            $this->info('Backup timestamp: ' . $timestamp);

            // Update last backup info
            $this->updateLastBackupInfo($timestamp);

        } catch (\Exception $e) {
            $this->error('❌ Backup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Clean old backup files (keep only last 10)
     */
    private function cleanOldBackups($backupPath)
    {
        $databaseBackups = glob($backupPath . '/database_*.sqlite');
        rsort($databaseBackups);
        
        // Keep only last 10 database backups
        if (count($databaseBackups) > 10) {
            $toDelete = array_slice($databaseBackups, 10);
            foreach ($toDelete as $file) {
                File::delete($file);
                $this->info('🗑️ Deleted old backup: ' . basename($file));
            }
        }

        // Clean old backup info files
        $infoFiles = glob($backupPath . '/backup_info_*.json');
        rsort($infoFiles);
        
        if (count($infoFiles) > 10) {
            $toDelete = array_slice($infoFiles, 10);
            foreach ($toDelete as $file) {
                File::delete($file);
            }
        }
    }

    /**
     * Update last backup information
     */
    private function updateLastBackupInfo($timestamp)
    {
        $lastBackupPath = storage_path('app/last_backup.json');
        $backupInfo = [
            'last_backup' => $timestamp,
            'last_backup_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
        
        File::put($lastBackupPath, json_encode($backupInfo, JSON_PRETTY_PRINT));
    }
}
