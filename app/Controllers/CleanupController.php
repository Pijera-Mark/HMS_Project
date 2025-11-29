<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CleanupController extends BaseController
{
    /**
     * Clean up old debug files and logs to reduce data usage
     */
    public function cleanup()
    {
        $cleaned = 0;
        $totalSize = 0;
        
        // Clean up old debugbar files (keep only last 5)
        $debugDir = WRITEPATH . 'debugbar';
        if (is_dir($debugDir)) {
            $files = glob($debugDir . '/*.json');
            rsort($files); // Sort by newest first
            
            // Keep only the 5 most recent files
            $filesToDelete = array_slice($files, 5);
            
            foreach ($filesToDelete as $file) {
                $size = filesize($file);
                if (unlink($file)) {
                    $cleaned++;
                    $totalSize += $size;
                }
            }
        }
        
        // Clean up old log files (keep only last 3 days)
        $logDir = WRITEPATH . 'logs';
        if (is_dir($logDir)) {
            $files = glob($logDir . '/log-*.log');
            
            foreach ($files as $file) {
                // Extract date from filename
                if (preg_match('/log-(\d{4}-\d{2}-\d{2})\.log/', $file, $matches)) {
                    $fileDate = new \DateTime($matches[1]);
                    $cutoffDate = new \DateTime('3 days ago');
                    
                    if ($fileDate < $cutoffDate) {
                        $size = filesize($file);
                        if (unlink($file)) {
                            $cleaned++;
                            $totalSize += $size;
                        }
                    }
                }
            }
        }
        
        // Clean up session files older than 24 hours
        $sessionDir = WRITEPATH . 'session';
        if (is_dir($sessionDir)) {
            $files = glob($sessionDir . '/ci_session_*');
            $cutoffTime = time() - (24 * 60 * 60); // 24 hours ago
            
            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime) {
                    $size = filesize($file);
                    if (unlink($file)) {
                        $cleaned++;
                        $totalSize += $size;
                    }
                }
            }
        }
        
        // Clear cache directory
        $cacheDir = WRITEPATH . 'cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            
            foreach ($files as $file) {
                if (is_file($file) && !in_array(basename($file), ['index.html', '.htaccess'])) {
                    $size = filesize($file);
                    if (unlink($file)) {
                        $cleaned++;
                        $totalSize += $size;
                    }
                }
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'files_cleaned' => $cleaned,
            'space_freed' => $this->formatBytes($totalSize),
            'space_freed_bytes' => $totalSize
        ]);
    }
    
    /**
     * Get current data usage statistics
     */
    public function getUsageStats()
    {
        $stats = [
            'debugbar' => $this->getDirectorySize(WRITEPATH . 'debugbar'),
            'logs' => $this->getDirectorySize(WRITEPATH . 'logs'),
            'cache' => $this->getDirectorySize(WRITEPATH . 'cache'),
            'sessions' => $this->getDirectorySize(WRITEPATH . 'session'),
            'uploads' => $this->getDirectorySize(WRITEPATH . 'uploads'),
        ];
        
        $total = array_sum($stats);
        
        return $this->response->setJSON([
            'success' => true,
            'stats' => array_map([$this, 'formatBytes'], $stats),
            'total' => $this->formatBytes($total),
            'total_bytes' => $total
        ]);
    }
    
    /**
     * Calculate directory size
     */
    private function getDirectorySize($dir)
    {
        $size = 0;
        
        if (!is_dir($dir)) {
            return $size;
        }
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
