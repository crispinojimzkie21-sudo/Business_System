<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Create a new backup
     */
    public function create(Request $request)
    {
        // Only super admin can create backups
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'You do not have permission to create backups.');
        }

        try {
            // Run the backup command
            $exitCode = Artisan::call('backup:database --auto');
            
            if ($exitCode === 0) {
                return redirect()->back()->with('success', 'Backup created successfully!');
            } else {
                return redirect()->back()->with('error', 'Backup failed. Please check logs.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * List all available backups
     */
    public function list(Request $request)
    {
        // Only super admin can view backups
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'You do not have permission to view backups.');
        }

        $backupPath = storage_path('backups');
        $backups = [];

        if (is_dir($backupPath)) {
            $databaseFiles = glob($backupPath . '/database_*.sqlite');
            
            foreach ($databaseFiles as $file) {
                $filename = basename($file);
                $timestamp = str_replace(['database_', '.sqlite'], '', $filename);
                
                $backups[] = [
                    'filename' => $filename,
                    'timestamp' => $timestamp,
                    'date' => Carbon::createFromFormat('Y-m-d_H-i-s', $timestamp),
                    'size' => $this->formatBytes(filesize($file)),
                    'path' => $file,
                ];
            }
            
            // Sort by date (newest first)
            usort($backups, function($a, $b) {
                return $b['date'] <=> $a['date'];
            });
        }

        return view('admin.backups', compact('backups'));
    }

    /**
     * Download a backup file
     */
    public function download(Request $request, $filename)
    {
        // Only super admin can download backups
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to download backups.');
        }

        $backupPath = storage_path('backups/' . $filename);
        
        if (!file_exists($backupPath)) {
            abort(404, 'Backup file not found.');
        }

        // Validate filename format
        if (!preg_match('/^database_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sqlite$/', $filename)) {
            abort(400, 'Invalid backup file.');
        }

        return response()->download($backupPath);
    }

    /**
     * Delete a backup file
     */
    public function delete(Request $request, $filename)
    {
        // Only super admin can delete backups
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'You do not have permission to delete backups.');
        }

        $backupPath = storage_path('backups/' . $filename);
        
        if (!file_exists($backupPath)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        // Validate filename format
        if (!preg_match('/^database_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sqlite$/', $filename)) {
            return redirect()->back()->with('error', 'Invalid backup file.');
        }

        try {
            File::delete($backupPath);
            
            // Also delete corresponding info file if it exists
            $infoFile = str_replace('database_', 'backup_info_', str_replace('.sqlite', '.json', $filename));
            $infoPath = storage_path('backups/' . $infoFile);
            if (file_exists($infoPath)) {
                File::delete($infoPath);
            }
            
            return redirect()->back()->with('success', 'Backup deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
