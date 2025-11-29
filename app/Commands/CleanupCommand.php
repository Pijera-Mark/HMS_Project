<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupCommand extends BaseCommand
{
    /**
     * The Command's Group.
     *
     * @var string
     */
    protected $group = 'Maintenance';

    /**
     * The Command's Name.
     *
     * @var string
     */
    protected $name = 'cleanup:system';

    /**
     * The Command's Description.
     *
     * @var string
     */
    protected $description = 'Clean up old files to reduce data usage';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Starting HMS cleanup...', 'green');
        
        $cleaned = 0;
        $totalSize = 0;
        
        // Clean up old debugbar files
        $debugDir = WRITEPATH . 'debugbar';
        if (is_dir($debugDir)) {
            CLI::write('Cleaning debugbar files...', 'yellow');
            $files = glob($debugDir . '/*.json');
            rsort($files);
            
            $filesToDelete = array_slice($files, 5);
            
            foreach ($filesToDelete as $file) {
                $size = filesize($file);
                if (unlink($file)) {
                    $cleaned++;
                    $totalSize += $size;
                    CLI::write("Deleted: " . basename($file) . " (" . $this->formatBytes($size) . ")", 'cyan');
                }
            }
            
            CLI::write("Debugbar cleanup: " . count($filesToDelete) . " files removed", 'green');
        }
        
        // Clean up old log files
        $logDir = WRITEPATH . 'logs';
        if (is_dir($logDir)) {
            CLI::write('Cleaning old log files...', 'yellow');
            $files = glob($logDir . '/log-*.log');
            
            foreach ($files as $file) {
                if (preg_match('/log-(\d{4}-\d{2}-\d{2})\.log/', $file, $matches)) {
                    $fileDate = new \DateTime($matches[1]);
                    $cutoffDate = new \DateTime('3 days ago');
                    
                    if ($fileDate < $cutoffDate) {
                        $size = filesize($file);
                        if (unlink($file)) {
                            $cleaned++;
                            $totalSize += $size;
                            CLI::write("Deleted: " . basename($file) . " (" . $this->formatBytes($size) . ")", 'cyan');
                        }
                    }
                }
            }
            
            CLI::write("Log cleanup completed", 'green');
        }
        
        // Clean up old session files
        $sessionDir = WRITEPATH . 'session';
        if (is_dir($sessionDir)) {
            CLI::write('Cleaning old session files...', 'yellow');
            $files = glob($sessionDir . '/ci_session_*');
            $cutoffTime = time() - (24 * 60 * 60);
            
            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime) {
                    $size = filesize($file);
                    if (unlink($file)) {
                        $cleaned++;
                        $totalSize += $size;
                    }
                }
            }
            
            CLI::write("Session cleanup completed", 'green');
        }
        
        // Clear cache
        $cacheDir = WRITEPATH . 'cache';
        if (is_dir($cacheDir)) {
            CLI::write('Clearing cache...', 'yellow');
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
            
            CLI::write("Cache cleanup completed", 'green');
        }
        
        CLI::write("\nCleanup Summary:", 'green');
        CLI::write("Files cleaned: {$cleaned}", 'white');
        CLI::write("Space freed: " . $this->formatBytes($totalSize), 'white');
        CLI::write("Cleanup completed successfully!", 'green');
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
