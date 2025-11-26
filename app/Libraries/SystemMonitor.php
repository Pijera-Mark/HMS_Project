<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use Config\Services;

/**
 * System Monitoring and Logging System
 * - Performance monitoring
 * - Error tracking
 * - Resource usage monitoring
 * - Alert system
 * - Log analysis
 * - Health checks
 */
class SystemMonitor
{
    protected array $metrics = [];
    protected array $alerts = [];
    protected array $config;
    protected float $startTime;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'enable_performance_monitoring' => true,
            'enable_error_tracking' => true,
            'enable_resource_monitoring' => true,
            'enable_alerts' => true,
            'log_retention_days' => 30,
            'alert_thresholds' => [
                'memory_usage' => 80, // percentage
                'cpu_usage' => 80, // percentage
                'disk_usage' => 85, // percentage
                'response_time' => 2000, // milliseconds
                'error_rate' => 5 // percentage
            ]
        ], $config);
        
        $this->startTime = microtime(true);
    }

    /**
     * Start monitoring
     */
    public function startMonitoring(): void
    {
        if ($this->config['enable_performance_monitoring']) {
            $this->startPerformanceMonitoring();
        }
        
        if ($this->config['enable_resource_monitoring']) {
            $this->startResourceMonitoring();
        }
        
        $this->logInfo('System monitoring started');
    }

    /**
     * Record performance metric
     */
    public function recordMetric(string $name, $value, array $tags = []): void
    {
        $metric = [
            'name' => $name,
            'value' => $value,
            'timestamp' => Time::now()->toISOString(),
            'tags' => $tags
        ];

        $this->metrics[] = $metric;
        
        // Check alert thresholds
        $this->checkAlertThresholds($name, $value, $tags);
        
        // Log if needed
        $this->logMetric($metric);
    }

    /**
     * Record API request performance
     */
    public function recordApiRequest(string $endpoint, string $method, int $statusCode, float $responseTime, array $metadata = []): void
    {
        $this->recordMetric('api_request_count', 1, [
            'endpoint' => $endpoint,
            'method' => $method,
            'status_code' => $statusCode,
            'success' => $statusCode < 400
        ]);

        $this->recordMetric('api_response_time', $responseTime, [
            'endpoint' => $endpoint,
            'method' => $method
        ]);

        if ($statusCode >= 400) {
            $this->recordMetric('api_error_count', 1, [
                'endpoint' => $endpoint,
                'method' => $method,
                'status_code' => $statusCode
            ]);
        }

        // Log slow requests
        if ($responseTime > $this->config['alert_thresholds']['response_time']) {
            $this->logWarning("Slow API request: {$method} {$endpoint} took {$responseTime}ms");
        }
    }

    /**
     * Record database query performance
     */
    public function recordDatabaseQuery(string $query, float $executionTime, int $affectedRows = 0): void
    {
        $this->recordMetric('db_query_count', 1, [
            'query_type' => $this->getQueryType($query)
        ]);

        $this->recordMetric('db_query_time', $executionTime, [
            'query_type' => $this->getQueryType($query)
        ]);

        if ($executionTime > 1000) { // 1 second
            $this->logWarning("Slow database query: {$query} took {$executionTime}ms");
        }
    }

    /**
     * Record cache performance
     */
    public function recordCacheOperation(string $operation, bool $hit, float $responseTime = 0): void
    {
        $this->recordMetric('cache_operation_count', 1, [
            'operation' => $operation,
            'hit' => $hit
        ]);

        if ($operation === 'get') {
            $this->recordMetric('cache_hit_rate', $hit ? 1 : 0);
        }

        if ($responseTime > 100) { // 100ms
            $this->logWarning("Slow cache operation: {$operation} took {$responseTime}ms");
        }
    }

    /**
     * Record error
     */
    public function recordError(\Throwable $error, array $context = []): void
    {
        $errorData = [
            'type' => get_class($error),
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
            'context' => $context,
            'timestamp' => Time::now()->toISOString()
        ];

        $this->recordMetric('error_count', 1, [
            'error_type' => $errorData['type'],
            'severity' => $this->getErrorSeverity($error)
        ]);

        $this->logError($errorData['message'], $errorData);
    }

    /**
     * Get system health status
     */
    public function getHealthStatus(): array
    {
        $status = [
            'overall' => 'healthy',
            'checks' => [],
            'metrics' => $this->getAggregatedMetrics(),
            'alerts' => $this->getActiveAlerts(),
            'timestamp' => Time::now()->toISOString()
        ];

        // Database health check
        $status['checks']['database'] = $this->checkDatabaseHealth();
        
        // Cache health check
        $status['checks']['cache'] = $this->checkCacheHealth();
        
        // File system health check
        $status['checks']['filesystem'] = $this->checkFileSystemHealth();
        
        // Memory usage check
        $status['checks']['memory'] = $this->checkMemoryUsage();
        
        // CPU usage check
        $status['checks']['cpu'] = $this->checkCpuUsage();
        
        // Determine overall status
        foreach ($status['checks'] as $check) {
            if ($check['status'] !== 'healthy') {
                $status['overall'] = 'degraded';
                break;
            }
        }

        return $status;
    }

    /**
     * Get performance report
     */
    public function getPerformanceReport(array $filters = []): array
    {
        $timeRange = $filters['time_range'] ?? '1h';
        $endTime = Time::now();
        $startTime = $endTime->copy()->sub($timeRange);

        $report = [
            'time_range' => [
                'start' => $startTime->toISOString(),
                'end' => $endTime->toISOString()
            ],
            'api_performance' => $this->getApiPerformanceMetrics($startTime, $endTime),
            'database_performance' => $this->getDatabasePerformanceMetrics($startTime, $endTime),
            'cache_performance' => $this->getCachePerformanceMetrics($startTime, $endTime),
            'error_summary' => $this->getErrorSummary($startTime, $endTime),
            'top_slow_requests' => $this->getTopSlowRequests($startTime, $endTime),
            'resource_usage' => $this->getResourceUsageMetrics($startTime, $endTime)
        ];

        return $report;
    }

    /**
     * Create alert
     */
    public function createAlert(string $type, string $message, array $metadata = [], string $severity = 'warning'): string
    {
        $alert = [
            'id' => uniqid('alert_', true),
            'type' => $type,
            'message' => $message,
            'severity' => $severity,
            'metadata' => $metadata,
            'created_at' => Time::now()->toISOString(),
            'resolved' => false,
            'resolved_at' => null
        ];

        $this->alerts[] = $alert;
        
        $this->logAlert($alert);
        
        return $alert['id'];
    }

    /**
     * Resolve alert
     */
    public function resolveAlert(string $alertId): bool
    {
        foreach ($this->alerts as &$alert) {
            if ($alert['id'] === $alertId) {
                $alert['resolved'] = true;
                $alert['resolved_at'] = Time::now()->toISOString();
                
                $this->logInfo("Alert resolved: {$alertId}");
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get active alerts
     */
    public function getActiveAlerts(): array
    {
        return array_filter($this->alerts, function($alert) {
            return !$alert['resolved'];
        });
    }

    /**
     * Check alert thresholds
     */
    protected function checkAlertThresholds(string $metricName, $value, array $tags): void
    {
        if (!$this->config['enable_alerts']) {
            return;
        }

        // Check memory usage
        if ($metricName === 'memory_usage' && $value > $this->config['alert_thresholds']['memory_usage']) {
            $this->createAlert('high_memory_usage', 
                "Memory usage is {$value}%, threshold is {$this->config['alert_thresholds']['memory_usage']}%",
                ['current_usage' => $value, 'threshold' => $this->config['alert_thresholds']['memory_usage']],
                'critical'
            );
        }

        // Check response time
        if ($metricName === 'api_response_time' && $value > $this->config['alert_thresholds']['response_time']) {
            $endpoint = $tags['endpoint'] ?? 'unknown';
            $this->createAlert('slow_api_response', 
                "API response time for {$endpoint} is {$value}ms, threshold is {$this->config['alert_thresholds']['response_time']}ms",
                ['endpoint' => $endpoint, 'response_time' => $value, 'threshold' => $this->config['alert_thresholds']['response_time']]
            );
        }

        // Check error rate
        if ($metricName === 'error_count' && $this->getErrorRate() > $this->config['alert_thresholds']['error_rate']) {
            $this->createAlert('high_error_rate', 
                "Error rate is {$this->getErrorRate()}%, threshold is {$this->config['alert_thresholds']['error_rate']}%",
                ['current_rate' => $this->getErrorRate(), 'threshold' => $this->config['alert_thresholds']['error_rate']],
                'critical'
            );
        }
    }

    /**
     * Check database health
     */
    protected function checkDatabaseHealth(): array
    {
        try {
            $db = Services::database();
            $result = $db->query("SELECT 1 as test")->getRow();
            
            if ($result && $result->test == 1) {
                return ['status' => 'healthy', 'message' => 'Database connection is working'];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }

        return ['status' => 'unhealthy', 'message' => 'Database test query failed'];
    }

    /**
     * Check cache health
     */
    protected function checkCacheHealth(): array
    {
        try {
            $cache = Services::cache();
            $testKey = 'health_check_' . time();
            $testValue = 'test_value';
            
            $cache->save($testKey, $testValue, 10);
            $retrieved = $cache->get($testKey);
            $cache->delete($testKey);
            
            if ($retrieved === $testValue) {
                return ['status' => 'healthy', 'message' => 'Cache is working properly'];
            }
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Cache test failed: ' . $e->getMessage()];
        }

        return ['status' => 'unhealthy', 'message' => 'Cache value mismatch'];
    }

    /**
     * Check file system health
     */
    protected function checkFileSystemHealth(): array
    {
        $writablePaths = [
            WRITEPATH . 'logs',
            WRITEPATH . 'cache',
            WRITEPATH . 'session'
        ];

        foreach ($writablePaths as $path) {
            if (!is_dir($path) || !is_writable($path)) {
                return ['status' => 'unhealthy', 'message' => "Path {$path} is not writable"];
            }
        }

        return ['status' => 'healthy', 'message' => 'All writable paths are accessible'];
    }

    /**
     * Check memory usage
     */
    protected function checkMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $usagePercent = ($memoryUsage / $memoryLimit) * 100;

        $status = $usagePercent < $this->config['alert_thresholds']['memory_usage'] ? 'healthy' : 'degraded';

        return [
            'status' => $status,
            'message' => "Memory usage is {$usagePercent}%",
            'usage_bytes' => $memoryUsage,
            'limit_bytes' => $memoryLimit,
            'usage_percent' => round($usagePercent, 2)
        ];
    }

    /**
     * Check CPU usage
     */
    protected function checkCpuUsage(): array
    {
        // This is a simplified CPU check
        // In a real implementation, you'd use system-specific commands
        $load = sys_getloadavg();
        
        if ($load === false) {
            return ['status' => 'unknown', 'message' => 'Could not determine CPU load'];
        }

        $currentLoad = $load[0];
        $cpuCores = $this->getCpuCores();
        $usagePercent = ($currentLoad / $cpuCores) * 100;

        $status = $usagePercent < $this->config['alert_thresholds']['cpu_usage'] ? 'healthy' : 'degraded';

        return [
            'status' => $status,
            'message' => "CPU usage is {$usagePercent}%",
            'load_1min' => $currentLoad,
            'load_5min' => $load[1],
            'load_15min' => $load[2],
            'cpu_cores' => $cpuCores,
            'usage_percent' => round($usagePercent, 2)
        ];
    }

    /**
     * Get aggregated metrics
     */
    protected function getAggregatedMetrics(): array
    {
        $aggregated = [];
        
        // Group metrics by name
        $grouped = [];
        foreach ($this->metrics as $metric) {
            $name = $metric['name'];
            if (!isset($grouped[$name])) {
                $grouped[$name] = [];
            }
            $grouped[$name][] = $metric['value'];
        }

        // Calculate aggregates
        foreach ($grouped as $name => $values) {
            if (is_numeric($values[0])) {
                $aggregated[$name] = [
                    'count' => count($values),
                    'sum' => array_sum($values),
                    'avg' => array_sum($values) / count($values),
                    'min' => min($values),
                    'max' => max($values)
                ];
            } else {
                $aggregated[$name] = ['count' => count($values)];
            }
        }

        return $aggregated;
    }

    /**
     * Get API performance metrics
     */
    protected function getApiPerformanceMetrics(Time $startTime, Time $endTime): array
    {
        $metrics = $this->filterMetricsByTimeRange('api_response_time', $startTime, $endTime);
        
        if (empty($metrics)) {
            return ['total_requests' => 0, 'avg_response_time' => 0, 'error_rate' => 0];
        }

        $responseTimes = array_column($metrics, 'value');
        $errorMetrics = $this->filterMetricsByTimeRange('api_error_count', $startTime, $endTime);
        $totalErrors = array_sum(array_column($errorMetrics, 'value'));

        return [
            'total_requests' => count($metrics),
            'avg_response_time' => array_sum($responseTimes) / count($responseTimes),
            'min_response_time' => min($responseTimes),
            'max_response_time' => max($responseTimes),
            'error_rate' => ($totalErrors / count($metrics)) * 100,
            'total_errors' => $totalErrors
        ];
    }

    /**
     * Get database performance metrics
     */
    protected function getDatabasePerformanceMetrics(Time $startTime, Time $endTime): array
    {
        $queryMetrics = $this->filterMetricsByTimeRange('db_query_time', $startTime, $endTime);
        
        if (empty($queryMetrics)) {
            return ['total_queries' => 0, 'avg_query_time' => 0];
        }

        $queryTimes = array_column($queryMetrics, 'value');

        return [
            'total_queries' => count($queryMetrics),
            'avg_query_time' => array_sum($queryTimes) / count($queryMetrics),
            'min_query_time' => min($queryTimes),
            'max_query_time' => max($queryTimes)
        ];
    }

    /**
     * Get cache performance metrics
     */
    protected function getCachePerformanceMetrics(Time $startTime, Time $endTime): array
    {
        $hitMetrics = $this->filterMetricsByTimeRange('cache_hit_rate', $startTime, $endTime);
        
        if (empty($hitMetrics)) {
            return ['total_operations' => 0, 'hit_rate' => 0];
        }

        $hits = array_sum(array_column($hitMetrics, 'value'));
        $totalOperations = count($hitMetrics);
        $hitRate = ($hits / $totalOperations) * 100;

        return [
            'total_operations' => $totalOperations,
            'hits' => $hits,
            'misses' => $totalOperations - $hits,
            'hit_rate' => $hitRate
        ];
    }

    /**
     * Get error summary
     */
    protected function getErrorSummary(Time $startTime, Time $endTime): array
    {
        $errorMetrics = $this->filterMetricsByTimeRange('error_count', $startTime, $endTime);
        
        $summary = ['total_errors' => 0, 'by_type' => []];
        
        foreach ($errorMetrics as $metric) {
            $summary['total_errors'] += $metric['value'];
            
            $type = $metric['tags']['error_type'] ?? 'unknown';
            if (!isset($summary['by_type'][$type])) {
                $summary['by_type'][$type] = 0;
            }
            $summary['by_type'][$type] += $metric['value'];
        }

        return $summary;
    }

    /**
     * Get top slow requests
     */
    protected function getTopSlowRequests(Time $startTime, Time $endTime, int $limit = 10): array
    {
        $metrics = $this->filterMetricsByTimeRange('api_response_time', $startTime, $endTime);
        
        // Sort by response time descending
        usort($metrics, function($a, $b) {
            return $b['value'] - $a['value'];
        });

        return array_slice($metrics, 0, $limit);
    }

    /**
     * Get resource usage metrics
     */
    protected function getResourceUsageMetrics(Time $startTime, Time $endTime): array
    {
        return [
            'memory_peak' => memory_get_peak_usage(true),
            'memory_current' => memory_get_usage(true),
            'execution_time' => (microtime(true) - $this->startTime) * 1000
        ];
    }

    /**
     * Filter metrics by time range
     */
    protected function filterMetricsByTimeRange(string $metricName, Time $startTime, Time $endTime): array
    {
        $filtered = [];
        
        foreach ($this->metrics as $metric) {
            if ($metric['name'] === $metricName) {
                $timestamp = Time::parse($metric['timestamp']);
                
                if ($timestamp >= $startTime && $timestamp <= $endTime) {
                    $filtered[] = $metric;
                }
            }
        }
        
        return $filtered;
    }

    /**
     * Get error rate
     */
    protected function getErrorRate(): float
    {
        $errorCount = 0;
        $requestCount = 0;
        
        foreach ($this->metrics as $metric) {
            if ($metric['name'] === 'error_count') {
                $errorCount += $metric['value'];
            } elseif ($metric['name'] === 'api_request_count') {
                $requestCount += $metric['value'];
            }
        }
        
        return $requestCount > 0 ? ($errorCount / $requestCount) * 100 : 0;
    }

    /**
     * Get query type
     */
    protected function getQueryType(string $query): string
    {
        $query = strtoupper(trim($query));
        
        if (strpos($query, 'SELECT') === 0) return 'SELECT';
        if (strpos($query, 'INSERT') === 0) return 'INSERT';
        if (strpos($query, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($query, 'DELETE') === 0) return 'DELETE';
        
        return 'OTHER';
    }

    /**
     * Get error severity
     */
    protected function getErrorSeverity(\Throwable $error): string
    {
        if ($error instanceof \Error) return 'critical';
        if ($error instanceof \PDOException) return 'critical';
        if ($error instanceof \RuntimeException) return 'warning';
        
        return 'info';
    }

    /**
     * Parse memory limit
     */
    protected function parseMemoryLimit(string $limit): int
    {
        $limit = strtoupper(trim($limit));
        $last = strtolower($limit[strlen($limit) - 1]);
        $value = (int) $limit;

        switch ($last) {
            case 'G': return $value * 1024 * 1024 * 1024;
            case 'M': return $value * 1024 * 1024;
            case 'K': return $value * 1024;
            default: return $value;
        }
    }

    /**
     * Get CPU cores
     */
    protected function getCpuCores(): int
    {
        // Try to get CPU core count
        if (function_exists('shell_exec')) {
            $cores = shell_exec('nproc 2>/dev/null || echo 1');
            return (int) trim($cores);
        }
        
        return 1; // Default fallback
    }

    /**
     * Log metric
     */
    protected function logMetric(array $metric): void
    {
        log_message('info', 'Metric: ' . json_encode($metric));
    }

    /**
     * Log alert
     */
    protected function logAlert(array $alert): void
    {
        log_message('warning', 'Alert: ' . json_encode($alert));
    }

    /**
     * Log info message
     */
    protected function logInfo(string $message): void
    {
        log_message('info', '[Monitor] ' . $message);
    }

    /**
     * Log warning message
     */
    protected function logWarning(string $message): void
    {
        log_message('warning', '[Monitor] ' . $message);
    }

    /**
     * Log error message
     */
    protected function logError(string $message, array $context = []): void
    {
        log_message('error', '[Monitor] ' . $message . ' Context: ' . json_encode($context));
    }

    /**
     * Start performance monitoring
     */
    protected function startPerformanceMonitoring(): void
    {
        // Register shutdown function to capture execution metrics
        register_shutdown_function(function() {
            $executionTime = (microtime(true) - $this->startTime) * 1000;
            $this->recordMetric('script_execution_time', $executionTime);
            $this->recordMetric('memory_peak_usage', memory_get_peak_usage(true));
        });
    }

    /**
     * Start resource monitoring
     */
    protected function startResourceMonitoring(): void
    {
        // This would typically use system monitoring tools
        // For now, we'll just record basic metrics
    }
}
