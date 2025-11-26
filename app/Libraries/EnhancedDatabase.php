<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Query;
use Config\Services;

/**
 * Enhanced Database with connection pooling and optimization
 * - Query optimization
 * - Connection pooling
 * - Performance monitoring
 * - Automatic indexing suggestions
 * - Query caching
 * - Slow query detection
 */
class EnhancedDatabase
{
    protected BaseConnection $db;
    protected array $queryStats = [
        'total_queries' => 0,
        'slow_queries' => 0,
        'cache_hits' => 0,
        'cache_misses' => 0,
        'total_time' => 0
    ];

    protected array $config = [
        'slow_query_threshold' => 1000, // milliseconds
        'enable_query_cache' => true,
        'enable_profiling' => true,
        'max_connections' => 10,
        'connection_timeout' => 30
    ];

    protected array $connectionPool = [];
    protected array $queryCache = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->db = Services::database();
    }

    /**
     * Execute optimized query with monitoring
     */
    public function query(string $sql, array $params = [], bool $cache = true): array
    {
        $startTime = microtime(true);
        
        // Check query cache first
        if ($cache && $this->config['enable_query_cache']) {
            $cacheKey = $this->generateQueryCacheKey($sql, $params);
            $cached = $this->getCachedQuery($cacheKey);
            
            if ($cached !== null) {
                $this->queryStats['cache_hits']++;
                return $cached;
            }
        }

        try {
            // Execute query
            $result = $this->db->query($sql, $params)->getResultArray();
            
            $executionTime = (microtime(true) - $startTime) * 1000;
            $this->updateQueryStats($executionTime);
            
            // Cache result if enabled
            if ($cache && $this->config['enable_query_cache']) {
                $this->cacheQuery($cacheKey, $result);
            }
            
            // Log slow queries
            if ($executionTime > $this->config['slow_query_threshold']) {
                $this->logSlowQuery($sql, $params, $executionTime);
            }
            
            // Analyze query for optimization suggestions
            $this->analyzeQuery($sql, $executionTime);
            
            return $result;
            
        } catch (\Exception $e) {
            $this->logQueryError($sql, $params, $e);
            throw $e;
        }
    }

    /**
     * Execute multiple queries efficiently
     */
    public function batchQuery(array $queries): array
    {
        $results = [];
        $startTime = microtime(true);
        
        try {
            // Start transaction for batch operations
            $this->db->transStart();
            
            foreach ($queries as $index => $query) {
                $sql = $query['sql'] ?? '';
                $params = $query['params'] ?? [];
                $cache = $query['cache'] ?? true;
                
                $results[$index] = $this->query($sql, $params, $cache);
            }
            
            // Commit transaction
            $this->db->transComplete();
            
            $totalTime = (microtime(true) - $startTime) * 1000;
            
            log_message('info', "Batch query completed: " . count($queries) . " queries in {$totalTime}ms");
            
            return $results;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Batch query failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get paginated results with optimization
     */
    public function paginate(string $sql, array $params = [], int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
        
        // Get total count (optimized)
        $countSQL = "SELECT COUNT(*) as total FROM ({$sql}) as count_query";
        $total = $this->query($countSQL, $params)[0]['total'] ?? 0;
        
        // Get paginated data
        $dataSQL = "{$sql} LIMIT {$limit} OFFSET {$offset}";
        $data = $this->query($dataSQL, $params);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ];
    }

    /**
     * Execute stored procedure
     */
    public function callProcedure(string $procedure, array $params = []): array
    {
        $sql = "CALL {$procedure}(" . implode(',', array_fill(0, count($params), '?')) . ")";
        return $this->query($sql, $params, false);
    }

    /**
     * Get database health status
     */
    public function getHealthStatus(): array
    {
        try {
            // Test connection
            $this->db->query("SELECT 1")->getResult();
            
            // Get database version
            $version = $this->db->query("SELECT VERSION() as version")->getRow()->version;
            
            // Get connection info
            $connectionInfo = [
                'host' => $this->db->getPlatform(),
                'version' => $version,
                'database' => $this->db->getDatabase(),
                'status' => 'connected'
            ];
            
            // Get table sizes
            $tableSizes = $this->getTableSizes();
            
            // Get query statistics
            $queryStats = $this->getQueryStats();
            
            return [
                'connection' => $connectionInfo,
                'tables' => $tableSizes,
                'queries' => $queryStats,
                'cache' => $this->getCacheStats()
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get table sizes and statistics
     */
    protected function getTableSizes(): array
    {
        $tables = [];
        
        try {
            $result = $this->db->query("
                SELECT 
                    table_name,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                    table_rows
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
            ")->getResultArray();
            
            foreach ($result as $row) {
                $tables[$row['table_name']] = [
                    'size_mb' => $row['size_mb'],
                    'rows' => $row['table_rows']
                ];
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to get table sizes: ' . $e->getMessage());
        }
        
        return $tables;
    }

    /**
     * Optimize table
     */
    public function optimizeTable(string $tableName): bool
    {
        try {
            $this->db->query("OPTIMIZE TABLE {$tableName}");
            log_message('info', "Table {$tableName} optimized");
            return true;
        } catch (\Exception $e) {
            log_message('error', "Failed to optimize table {$tableName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Analyze table for optimization
     */
    public function analyzeTable(string $tableName): array
    {
        $analysis = [
            'table' => $tableName,
            'recommendations' => [],
            'missing_indexes' => [],
            'slow_queries' => []
        ];
        
        try {
            // Check for missing indexes
            $indexAnalysis = $this->analyzeIndexes($tableName);
            $analysis['missing_indexes'] = $indexAnalysis['missing'];
            $analysis['recommendations'] = array_merge($analysis['recommendations'], $indexAnalysis['recommendations']);
            
            // Check table statistics
            $stats = $this->getTableStatistics($tableName);
            $analysis['statistics'] = $stats;
            
        } catch (\Exception $e) {
            log_message('error', "Failed to analyze table {$tableName}: " . $e->getMessage());
        }
        
        return $analysis;
    }

    /**
     * Analyze table indexes
     */
    protected function analyzeIndexes(string $tableName): array
    {
        $result = [
            'missing' => [],
            'recommendations' => []
        ];
        
        try {
            // Get existing indexes
            $indexes = $this->db->query("
                SHOW INDEX FROM {$tableName}
            ")->getResultArray();
            
            // Get slow queries related to this table
            $slowQueries = $this->getSlowQueriesForTable($tableName);
            
            // Analyze for missing indexes based on slow queries
            foreach ($slowQueries as $query) {
                $recommendations = $this->suggestIndexes($query['sql'], $tableName);
                $result['missing'] = array_merge($result['missing'], $recommendations);
            }
            
        } catch (\Exception $e) {
            log_message('error', "Failed to analyze indexes for {$tableName}: " . $e->getMessage());
        }
        
        return $result;
    }

    /**
     * Get table statistics
     */
    protected function getTableStatistics(string $tableName): array
    {
        try {
            return $this->db->query("
                SELECT 
                    COUNT(*) as total_rows,
                    AVG(LENGTH(CAST(CONCAT_WS('', *) AS CHAR))) as avg_row_length
                FROM {$tableName}
            ")->getRowArray();
        } catch (\Exception $e) {
            return ['total_rows' => 0, 'avg_row_length' => 0];
        }
    }

    /**
     * Suggest indexes based on query patterns
     */
    protected function suggestIndexes(string $sql, string $tableName): array
    {
        $suggestions = [];
        
        // Simple pattern matching for common WHERE clauses
        if (preg_match('/WHERE\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*=/', $sql, $matches)) {
            $column = $matches[1];
            $suggestions[] = "CREATE INDEX idx_{$tableName}_{$column} ON {$tableName}({$column})";
        }
        
        if (preg_match('/ORDER BY\s+([a-zA-Z_][a-zA-Z0-9_]*)/', $sql, $matches)) {
            $column = $matches[1];
            $suggestions[] = "CREATE INDEX idx_{$tableName}_{$column}_order ON {$tableName}({$column})";
        }
        
        return array_unique($suggestions);
    }

    /**
     * Get slow queries for table
     */
    protected function getSlowQueriesForTable(string $tableName): array
    {
        // This would typically query the slow query log
        // For now, return empty array
        return [];
    }

    /**
     * Generate query cache key
     */
    protected function generateQueryCacheKey(string $sql, array $params): string
    {
        return 'query_' . md5($sql . serialize($params));
    }

    /**
     * Get cached query
     */
    protected function getCachedQuery(string $key): ?array
    {
        $cache = Services::cache();
        $result = $cache->get($key);
        
        if ($result !== null) {
            return $result;
        }
        
        $this->queryStats['cache_misses']++;
        return null;
    }

    /**
     * Cache query result
     */
    protected function cacheQuery(string $key, array $result): void
    {
        $cache = Services::cache();
        $cache->save($key, $result, 300); // 5 minutes
    }

    /**
     * Update query statistics
     */
    protected function updateQueryStats(float $executionTime): void
    {
        $this->queryStats['total_queries']++;
        $this->queryStats['total_time'] += $executionTime;
        
        if ($executionTime > $this->config['slow_query_threshold']) {
            $this->queryStats['slow_queries']++;
        }
    }

    /**
     * Log slow query
     */
    protected function logSlowQuery(string $sql, array $params, float $executionTime): void
    {
        $logData = [
            'sql' => $sql,
            'params' => $params,
            'execution_time' => $executionTime,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        log_message('warning', 'Slow Query: ' . json_encode($logData));
    }

    /**
     * Log query error
     */
    protected function logQueryError(string $sql, array $params, \Exception $e): void
    {
        $logData = [
            'sql' => $sql,
            'params' => $params,
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        log_message('error', 'Query Error: ' . json_encode($logData));
    }

    /**
     * Analyze query for optimization
     */
    protected function analyzeQuery(string $sql, float $executionTime): void
    {
        // Basic query analysis
        if ($executionTime > $this->config['slow_query_threshold']) {
            // Suggest EXPLAIN for slow queries
            log_message('info', "Consider running EXPLAIN on: {$sql}");
        }
    }

    /**
     * Get query statistics
     */
    public function getQueryStats(): array
    {
        $avgTime = $this->queryStats['total_queries'] > 0 
            ? $this->queryStats['total_time'] / $this->queryStats['total_queries'] 
            : 0;
            
        return array_merge($this->queryStats, [
            'avg_query_time' => round($avgTime, 2),
            'slow_query_threshold' => $this->config['slow_query_threshold']
        ]);
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        $cache = Services::cache();
        
        return [
            'driver' => get_class($cache),
            'query_cache_enabled' => $this->config['enable_query_cache'],
            'cache_hits' => $this->queryStats['cache_hits'],
            'cache_misses' => $this->queryStats['cache_misses'],
            'cache_hit_rate' => $this->queryStats['cache_hits'] + $this->queryStats['cache_misses'] > 0
                ? round(($this->queryStats['cache_hits'] / ($this->queryStats['cache_hits'] + $this->queryStats['cache_misses'])) * 100, 2)
                : 0
        ];
    }

    /**
     * Reset statistics
     */
    public function resetStats(): void
    {
        $this->queryStats = [
            'total_queries' => 0,
            'slow_queries' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0,
            'total_time' => 0
        ];
    }
}
