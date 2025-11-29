<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GitCleanupCommand extends BaseCommand
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
    protected $name = 'git:cleanup';

    /**
     * The Command's Description.
     *
     * @var string
     */
    protected $description = 'Clean up Git repository to reduce storage usage';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Git Repository Cleanup', 'green');
        CLI::write('======================', 'green');
        
        $gitPath = ROOTPATH . '.git';
        
        if (!is_dir($gitPath)) {
            CLI::write('No .git directory found. This is not a Git repository.', 'red');
            return;
        }
        
        // Get initial size
        $initialSize = $this->getDirectorySize(ROOTPATH);
        CLI::write("Initial repository size: " . $this->formatBytes($initialSize), 'yellow');
        
        CLI::write("\nStep 1: Running Git garbage collection...", 'yellow');
        $this->executeGitCommand(['git', 'gc', '--aggressive', '--prune=now']);
        
        CLI::write("\nStep 2: Cleaning up unnecessary files...", 'yellow');
        $this->executeGitCommand(['git', 'clean', '-fd']);
        
        CLI::write("\nStep 3: Compressing Git database...", 'yellow');
        $this->executeGitCommand(['git', 'repack', '-a', '-d', '--depth=250', '--window=250']);
        
        CLI::write("\nStep 4: Final cleanup...", 'yellow');
        $this->executeGitCommand(['git', 'gc', '--prune=now']);
        
        // Get final size
        $finalSize = $this->getDirectorySize(ROOTPATH);
        $reduction = $initialSize - $finalSize;
        $percentage = $initialSize > 0 ? round(($reduction / $initialSize) * 100, 1) : 0;
        
        CLI::write("\nResults:", 'green');
        CLI::write("--------", 'green');
        CLI::write("Initial size: " . $this->formatBytes($initialSize), 'white');
        CLI::write("Final size: " . $this->formatBytes($finalSize), 'white');
        CLI::write("Space freed: " . $this->formatBytes($reduction), 'green');
        CLI::write("Reduction: {$percentage}%", 'green');
        
        if ($reduction > 0) {
            CLI::write("\nGit cleanup completed successfully!", 'green');
            CLI::write("The HMS should now use significantly less data.", 'white');
        } else {
            CLI::write("\nNo significant reduction achieved.", 'yellow');
            CLI::write("The repository may already be optimized.", 'white');
        }
    }
    
    /**
     * Execute a Git command
     */
    private function executeGitCommand($command)
    {
        $descriptorspec = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"],  // stderr
        ];
        
        $process = proc_open(implode(' ', $command), $descriptorspec, $pipes, ROOTPATH);
        
        if (is_resource($process)) {
            fclose($pipes[0]);
            
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            
            $exit_code = proc_close($process);
            
            if ($exit_code !== 0) {
                CLI::write("Error: " . trim($error), 'red');
                return false;
            }
            
            return $output;
        }
        
        return false;
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
