<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use Config\Services;

/**
 * Background Job Processor
 * - Queue management
 * - Job scheduling
 * - Worker processes
 * - Failed job handling
 * - Job prioritization
 * - Progress tracking
 */
class BackgroundJobProcessor
{
    protected array $jobs = [];
    protected array $workers = [];
    protected array $config;
    protected bool $isRunning = false;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'max_workers' => 4,
            'max_retry_attempts' => 3,
            'retry_delay' => 60, // seconds
            'job_timeout' => 300, // seconds
            'cleanup_interval' => 3600, // seconds
            'enable_logging' => true,
            'queue_storage' => 'database' // database, file, redis
        ], $config);
    }

    /**
     * Add job to queue
     */
    public function addJob(string $type, array $payload, array $options = []): string
    {
        $job = [
            'id' => uniqid('job_', true),
            'type' => $type,
            'payload' => $payload,
            'status' => 'pending',
            'priority' => $options['priority'] ?? 5, // 1-10, 10 being highest
            'attempts' => 0,
            'max_attempts' => $options['max_attempts'] ?? $this->config['max_retry_attempts'],
            'created_at' => Time::now()->toISOString(),
            'scheduled_at' => $options['scheduled_at'] ?? Time::now()->toISOString(),
            'timeout' => $options['timeout'] ?? $this->config['job_timeout'],
            'metadata' => $options['metadata'] ?? []
        ];

        $this->jobs[$job['id']] = $job;
        
        // Save to storage
        $this->saveJob($job);
        
        $this->logInfo("Job added to queue: {$job['id']} ({$type})");
        
        return $job['id'];
    }

    /**
     * Schedule job for later execution
     */
    public function scheduleJob(string $type, array $payload, \DateTime $scheduleTime, array $options = []): string
    {
        $options['scheduled_at'] = $scheduleTime->format('Y-m-d H:i:s');
        return $this->addJob($type, $payload, $options);
    }

    /**
     * Start job processor
     */
    public function start(): void
    {
        $this->isRunning = true;
        
        $this->logInfo("Background job processor started with {$this->config['max_workers']} workers");
        
        // Load existing jobs
        $this->loadJobs();
        
        // Start workers
        for ($i = 0; $i < $this->config['max_workers']; $i++) {
            $this->startWorker($i);
        }
        
        // Main processing loop
        while ($this->isRunning) {
            $this->processQueue();
            $this->cleanupFailedJobs();
            sleep(1);
        }
    }

    /**
     * Stop job processor
     */
    public function stop(): void
    {
        $this->isRunning = false;
        
        // Wait for workers to finish
        foreach ($this->workers as $workerId => $worker) {
            if ($worker['running']) {
                $this->stopWorker($workerId);
            }
        }
        
        $this->logInfo('Background job processor stopped');
    }

    /**
     * Get job status
     */
    public function getJobStatus(string $jobId): ?array
    {
        return $this->jobs[$jobId] ?? null;
    }

    /**
     * Get queue statistics
     */
    public function getQueueStats(): array
    {
        $stats = [
            'total_jobs' => count($this->jobs),
            'pending' => 0,
            'running' => 0,
            'completed' => 0,
            'failed' => 0,
            'workers' => count($this->workers),
            'active_workers' => 0
        ];

        foreach ($this->jobs as $job) {
            $stats[$job['status']]++;
        }

        foreach ($this->workers as $worker) {
            if ($worker['running']) {
                $stats['active_workers']++;
            }
        }

        return $stats;
    }

    /**
     * Process queued jobs
     */
    protected function processQueue(): void
    {
        $now = Time::now();
        
        // Get pending jobs scheduled for now
        $pendingJobs = array_filter($this->jobs, function($job) use ($now) {
            return $job['status'] === 'pending' && 
                   Time::parse($job['scheduled_at']) <= $now;
        });

        // Sort by priority (highest first)
        uasort($pendingJobs, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        // Assign jobs to available workers
        foreach ($pendingJobs as $jobId => $job) {
            $workerId = $this->getAvailableWorker();
            
            if ($workerId !== null) {
                $this->assignJobToWorker($jobId, $workerId);
            } else {
                break; // No available workers
            }
        }
    }

    /**
     * Start worker
     */
    protected function startWorker(int $workerId): void
    {
        $this->workers[$workerId] = [
            'id' => $workerId,
            'running' => true,
            'current_job' => null,
            'started_at' => Time::now()->getTimestamp()
        ];

        // In a real implementation, this would spawn actual processes
        // For now, we'll simulate worker behavior
    }

    /**
     * Stop worker
     */
    protected function stopWorker(int $workerId): void
    {
        if (isset($this->workers[$workerId])) {
            $this->workers[$workerId]['running'] = false;
            
            // Mark current job as failed if still running
            $currentJobId = $this->workers[$workerId]['current_job'];
            if ($currentJobId && isset($this->jobs[$currentJobId])) {
                $this->markJobFailed($currentJobId, 'Worker stopped');
            }
        }
    }

    /**
     * Get available worker
     */
    protected function getAvailableWorker(): ?int
    {
        foreach ($this->workers as $workerId => $worker) {
            if ($worker['running'] && $worker['current_job'] === null) {
                return $workerId;
            }
        }
        return null;
    }

    /**
     * Assign job to worker
     */
    protected function assignJobToWorker(string $jobId, int $workerId): void
    {
        $this->jobs[$jobId]['status'] = 'running';
        $this->jobs[$jobId]['started_at'] = Time::now()->toISOString();
        $this->workers[$workerId]['current_job'] = $jobId;
        
        $this->saveJob($this->jobs[$jobId]);
        
        // Execute job (in real implementation, this would be async)
        $this->executeJob($jobId, $workerId);
    }

    /**
     * Execute job
     */
    protected function executeJob(string $jobId, int $workerId): void
    {
        $job = $this->jobs[$jobId];
        
        try {
            $this->logInfo("Executing job: {$jobId} ({$job['type']})");
            
            // Update job progress
            $this->updateJobProgress($jobId, 0, 'Starting job execution');
            
            // Execute based on job type
            $result = $this->executeJobByType($job);
            
            // Mark as completed
            $this->markJobCompleted($jobId, $result);
            
        } catch (\Exception $e) {
            $this->logError("Job execution failed: {$jobId} - " . $e->getMessage());
            
            // Mark as failed or retry
            if ($job['attempts'] < $job['max_attempts']) {
                $this->retryJob($jobId);
            } else {
                $this->markJobFailed($jobId, $e->getMessage());
            }
        } finally {
            // Free up worker
            $this->workers[$workerId]['current_job'] = null;
        }
    }

    /**
     * Execute job by type
     */
    protected function executeJobByType(array $job): array
    {
        $type = $job['type'];
        $payload = $job['payload'];
        
        switch ($type) {
            case 'send_email':
                return $this->executeEmailJob($payload);
                
            case 'generate_report':
                return $this->executeReportJob($payload);
                
            case 'process_payment':
                return $this->executePaymentJob($payload);
                
            case 'backup_database':
                return $this->executeBackupJob($payload);
                
            case 'send_notification':
                return $this->executeNotificationJob($payload);
                
            case 'cleanup_logs':
                return $this->executeCleanupJob($payload);
                
            case 'sync_data':
                return $this->executeSyncJob($payload);
                
            default:
                throw new \Exception("Unknown job type: {$type}");
        }
    }

    /**
     * Execute email job
     */
    protected function executeEmailJob(array $payload): array
    {
        $this->updateJobProgress($payload['job_id'], 25, 'Preparing email');
        
        // Simulate email sending
        sleep(1);
        
        $this->updateJobProgress($payload['job_id'], 50, 'Sending email');
        
        sleep(1);
        
        $this->updateJobProgress($payload['job_id'], 75, 'Email sent');
        
        sleep(1);
        
        return [
            'status' => 'success',
            'message' => 'Email sent successfully',
            'recipient' => $payload['to'],
            'sent_at' => Time::now()->toISOString()
        ];
    }

    /**
     * Execute report job
     */
    protected function executeReportJob(array $payload): array
    {
        $this->updateJobProgress($payload['job_id'], 10, 'Fetching data');
        
        // Simulate report generation
        sleep(2);
        
        $this->updateJobProgress($payload['job_id'], 50, 'Processing data');
        
        sleep(2);
        
        $this->updateJobProgress($payload['job_id'], 80, 'Generating report');
        
        sleep(1);
        
        return [
            'status' => 'success',
            'message' => 'Report generated successfully',
            'report_type' => $payload['type'],
            'generated_at' => Time::now()->toISOString(),
            'file_path' => WRITEPATH . 'reports/' . $payload['filename']
        ];
    }

    /**
     * Execute payment job
     */
    protected function executePaymentJob(array $payload): array
    {
        $this->updateJobProgress($payload['job_id'], 25, 'Processing payment');
        
        // Simulate payment processing
        sleep(1);
        
        $this->updateJobProgress($payload['job_id'], 75, 'Confirming payment');
        
        sleep(1);
        
        return [
            'status' => 'success',
            'message' => 'Payment processed successfully',
            'payment_id' => $payload['payment_id'],
            'amount' => $payload['amount'],
            'processed_at' => Time::now()->toISOString()
        ];
    }

    /**
     * Execute backup job
     */
    protected function executeBackupJob(array $payload): array
    {
        $this->updateJobProgress($payload['job_id'], 10, 'Starting backup');
        
        // Simulate backup process
        sleep(3);
        
        $this->updateJobProgress($payload['job_id'], 50, 'Creating backup');
        
        sleep(3);
        
        $this->updateJobProgress($payload['job_id'], 90, 'Finalizing backup');
        
        sleep(1);
        
        return [
            'status' => 'success',
            'message' => 'Backup completed successfully',
            'backup_file' => $payload['filename'],
            'size' => '1024MB',
            'created_at' => Time::now()->toISOString()
        ];
    }

    /**
     * Execute notification job
     */
    protected function executeNotificationJob(array $payload): array
    {
        $this->updateJobProgress($payload['job_id'], 50, 'Sending notification');
        
        // Simulate notification sending
        sleep(1);
        
        return [
            'status' => 'success',
            'message' => 'Notification sent successfully',
            'recipient' => $payload['user_id'],
            'type' => $payload['type'],
            'sent_at' => Time::now()->toISOString()
        ];
    }

    /**
     * Execute cleanup job
     */
    protected function executeCleanupJob(array $payload): array
    {
        $this->updateJobProgress($payload['job_id'], 25, 'Cleaning old files');
        
        // Simulate cleanup process
        sleep(2);
        
        $this->updateJobProgress($payload['job_id'], 75, 'Optimizing storage');
        
        sleep(1);
        
        return [
            'status' => 'success',
            'message' => 'Cleanup completed successfully',
            'files_deleted' => 150,
            'space_freed' => '500MB',
            'completed_at' => Time::now()->toISOString()
        ];
    }

    /**
     * Execute sync job
     */
    protected function executeSyncJob(array $payload): array
    {
        $this->updateJobProgress($payload['job_id'], 20, 'Starting synchronization');
        
        // Simulate sync process
        sleep(2);
        
        $this->updateJobProgress($payload['job_id'], 60, 'Syncing data');
        
        sleep(2);
        
        $this->updateJobProgress($payload['job_id'], 90, 'Finalizing sync');
        
        sleep(1);
        
        return [
            'status' => 'success',
            'message' => 'Data synchronization completed',
            'records_synced' => 1000,
            'synced_at' => Time::now()->toISOString()
        ];
    }

    /**
     * Update job progress
     */
    protected function updateJobProgress(string $jobId, int $percentage, string $message = ''): void
    {
        if (isset($this->jobs[$jobId])) {
            $this->jobs[$jobId]['progress'] = $percentage;
            $this->jobs[$jobId]['progress_message'] = $message;
            $this->jobs[$jobId]['updated_at'] = Time::now()->toISOString();
            
            $this->saveJob($this->jobs[$jobId]);
        }
    }

    /**
     * Mark job as completed
     */
    protected function markJobCompleted(string $jobId, array $result): void
    {
        if (isset($this->jobs[$jobId])) {
            $this->jobs[$jobId]['status'] = 'completed';
            $this->jobs[$jobId]['completed_at'] = Time::now()->toISOString();
            $this->jobs[$jobId]['result'] = $result;
            $this->jobs[$jobId]['progress'] = 100;
            
            $this->saveJob($this->jobs[$jobId]);
            
            $this->logInfo("Job completed: {$jobId}");
        }
    }

    /**
     * Mark job as failed
     */
    protected function markJobFailed(string $jobId, string $error): void
    {
        if (isset($this->jobs[$jobId])) {
            $this->jobs[$jobId]['status'] = 'failed';
            $this->jobs[$jobId]['failed_at'] = Time::now()->toISOString();
            $this->jobs[$jobId]['error'] = $error;
            
            $this->saveJob($this->jobs[$jobId]);
            
            $this->logError("Job failed: {$jobId} - {$error}");
        }
    }

    /**
     * Retry job
     */
    protected function retryJob(string $jobId): void
    {
        if (isset($this->jobs[$jobId])) {
            $this->jobs[$jobId]['status'] = 'pending';
            $this->jobs[$jobId]['attempts']++;
            $this->jobs[$jobId]['scheduled_at'] = Time::now()->addSeconds($this->config['retry_delay'])->toISOString();
            
            $this->saveJob($this->jobs[$jobId]);
            
            $this->logInfo("Job queued for retry: {$jobId} (attempt {$this->jobs[$jobId]['attempts']})");
        }
    }

    /**
     * Cleanup failed jobs
     */
    protected function cleanupFailedJobs(): void
    {
        $cutoffTime = Time::now()->subDays(7); // Keep failed jobs for 7 days
        
        foreach ($this->jobs as $jobId => $job) {
            if ($job['status'] === 'failed' && Time::parse($job['failed_at']) < $cutoffTime) {
                unset($this->jobs[$jobId]);
                $this->deleteJob($jobId);
            }
        }
    }

    /**
     * Save job to storage
     */
    protected function saveJob(array $job): void
    {
        switch ($this->config['queue_storage']) {
            case 'database':
                $this->saveJobToDatabase($job);
                break;
            case 'file':
                $this->saveJobToFile($job);
                break;
            case 'redis':
                $this->saveJobToRedis($job);
                break;
        }
    }

    /**
     * Load jobs from storage
     */
    protected function loadJobs(): void
    {
        switch ($this->config['queue_storage']) {
            case 'database':
                $this->loadJobsFromDatabase();
                break;
            case 'file':
                $this->loadJobsFromFile();
                break;
            case 'redis':
                $this->loadJobsFromRedis();
                break;
        }
    }

    /**
     * Delete job from storage
     */
    protected function deleteJob(string $jobId): void
    {
        switch ($this->config['queue_storage']) {
            case 'database':
                $this->deleteJobFromDatabase($jobId);
                break;
            case 'file':
                $this->deleteJobFromFile($jobId);
                break;
            case 'redis':
                $this->deleteJobFromRedis($jobId);
                break;
        }
    }

    /**
     * Save job to database (placeholder)
     */
    protected function saveJobToDatabase(array $job): void
    {
        // Implementation would save to database table
    }

    /**
     * Load jobs from database (placeholder)
     */
    protected function loadJobsFromDatabase(): void
    {
        // Implementation would load from database table
    }

    /**
     * Delete job from database (placeholder)
     */
    protected function deleteJobFromDatabase(string $jobId): void
    {
        // Implementation would delete from database table
    }

    /**
     * Save job to file
     */
    protected function saveJobToFile(array $job): void
    {
        $jobFile = WRITEPATH . 'jobs/' . $job['id'] . '.json';
        $jobDir = dirname($jobFile);
        
        if (!is_dir($jobDir)) {
            mkdir($jobDir, 0755, true);
        }
        
        file_put_contents($jobFile, json_encode($job));
    }

    /**
     * Load jobs from file
     */
    protected function loadJobsFromFile(): void
    {
        $jobDir = WRITEPATH . 'jobs/';
        
        if (!is_dir($jobDir)) {
            return;
        }
        
        foreach (glob($jobDir . '*.json') as $jobFile) {
            $jobData = json_decode(file_get_contents($jobFile), true);
            if ($jobData) {
                $this->jobs[$jobData['id']] = $jobData;
            }
        }
    }

    /**
     * Delete job from file
     */
    protected function deleteJobFromFile(string $jobId): void
    {
        $jobFile = WRITEPATH . 'jobs/' . $jobId . '.json';
        if (file_exists($jobFile)) {
            unlink($jobFile);
        }
    }

    /**
     * Save job to Redis (placeholder)
     */
    protected function saveJobToRedis(array $job): void
    {
        // Implementation would save to Redis
    }

    /**
     * Load jobs from Redis (placeholder)
     */
    protected function loadJobsFromRedis(): void
    {
        // Implementation would load from Redis
    }

    /**
     * Delete job from Redis (placeholder)
     */
    protected function deleteJobFromRedis(string $jobId): void
    {
        // Implementation would delete from Redis
    }

    /**
     * Log info message
     */
    protected function logInfo(string $message): void
    {
        if ($this->config['enable_logging']) {
            log_message('info', '[JobProcessor] ' . $message);
        }
    }

    /**
     * Log error message
     */
    protected function logError(string $message): void
    {
        if ($this->config['enable_logging']) {
            log_message('error', '[JobProcessor] ' . $message);
        }
    }
}
