<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DiagnoseCommand extends BaseCommand
{
    /**
     * The Command's Group.
     *
     * @var string
     */
    protected $group = 'Diagnostics';

    /**
     * The Command's Name.
     *
     * @var string
     */
    protected $name = 'diagnose:storage';

    /**
     * The Command's Description.
     *
     * @var string
     */
    protected $description = 'Diagnose storage usage and find large files';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('HMS Storage Diagnostics', 'green');
        CLI::write('========================', 'green');
        
        $totalSize = 0;
        $directories = [
            'writable' => WRITEPATH,
            'vendor' => ROOTPATH . 'vendor',
            'app' => APPPATH,
            'public' => ROOTPATH . 'public',
            'system' => SYSTEMPATH,
            'root' => ROOTPATH,
        ];
        
        CLI::write("\nDirectory Sizes:", 'yellow');
        CLI::write(str_repeat('-', 50), 'yellow');
        
        foreach ($directories as $name => $path) {
            if (is_dir($path)) {
                $size = $this->getDirectorySize($path);
                $totalSize += $size;
                CLI::write(sprintf("%-10s: %s", $name, $this->formatBytes($size)), 'white');
            }
        }
        
        CLI::write(str_repeat('-', 50), 'yellow');
        CLI::write(sprintf("Total Size: %s", $this->formatBytes($totalSize)), 'green');
        
        // Find large files
        CLI::write("\nLarge Files (>1MB):", 'yellow');
        CLI::write(str_repeat('-', 50), 'yellow');
        
        $largeFiles = $this->findLargeFiles(ROOTPATH, 1 * 1024 * 1024); // 1MB
        
        if (empty($largeFiles)) {
            CLI::write("No files larger than 1MB found.", 'white');
        } else {
            foreach ($largeFiles as $file) {
                $relativePath = str_replace(ROOTPATH, '', $file['path']);
                CLI::write(sprintf("%-50s: %s", substr($relativePath, -50), $this->formatBytes($file['size'])), 'white');
            }
        }
        
        // Check for potential issues
        CLI::write("\nPotential Issues:", 'yellow');
        CLI::write(str_repeat('-', 50), 'yellow');
        
        // Check vendor directory
        $vendorPath = ROOTPATH . 'vendor';
        if (is_dir($vendorPath)) {
            $vendorSize = $this->getDirectorySize($vendorPath);
            if ($vendorSize > 100 * 1024 * 1024) { // 100MB
                CLI::write("Vendor directory is large: " . $this->formatBytes($vendorSize), 'red');
                CLI::write("Consider running: composer install --no-dev --optimize-autoloader", 'white');
            }
        }
        
        // Check logs
        $logPath = WRITEPATH . 'logs';
        if (is_dir($logPath)) {
            $logSize = $this->getDirectorySize($logPath);
            if ($logSize > 10 * 1024 * 1024) { // 10MB
                CLI::write("Log directory is large: " . $this->formatBytes($logSize), 'red');
                CLI::write("Consider running: php spark cleanup:system", 'white');
            }
        }
        
        // Check debugbar
        $debugPath = WRITEPATH . 'debugbar';
        if (is_dir($debugPath)) {
            $debugSize = $this->getDirectorySize($debugPath);
            if ($debugSize > 5 * 1024 * 1024) { // 5MB
                CLI::write("Debugbar directory is large: " . $this->formatBytes($debugSize), 'red');
                CLI::write("Consider reducing debugbar history in config/Toolbar.php", 'white');
            }
        }
        
        CLI::write("\nDiagnostics completed.", 'green');
    }
    
    /**
     * Calculate directory size recursively
     */
    private function getDirectorySize($dir)
    {
        $size = 0;
        
        if (!is_dir($dir)) {
            return $size;
        }
        
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        } catch (\Exception $e) {
            CLI::write("Error scanning directory: " . $dir, 'red');
        }
        
        return $size;
    }
    
    /**
     * Find files larger than specified size
     */
    private function findLargeFiles($dir, $minSize)
    {
        $largeFiles = [];
        
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getSize() > $minSize) {
                    $largeFiles[] = [
                        'path' => $file->getPathname(),
                        'size' => $file->getSize()
                    ];
                }
            }
        } catch (\Exception $e) {
            CLI::write("Error finding large files: " . $e->getMessage(), 'red');
        }
        
        // Sort by size descending
        usort($largeFiles, function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        return array_slice($largeFiles, 0, 20); // Return top 20
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
