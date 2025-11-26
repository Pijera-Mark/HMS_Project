<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Performance extends BaseConfig
{
    /**
     * Enable/disable caching
     */
    public $enableCaching = true;

    /**
     * Cache TTL in seconds
     */
    public $cacheTTL = 3600; // 1 hour

    /**
     * Enable query logging
     */
    public $enableQueryLog = false;

    /**
     * Enable performance monitoring
     */
    public $enablePerformanceMonitoring = true;

    /**
     * Slow query threshold in milliseconds
     */
    public $slowQueryThreshold = 1000;

    /**
     * Enable database connection pooling
     */
    public $enableConnectionPooling = true;

    /**
     * Maximum database connections
     */
    public $maxDatabaseConnections = 10;

    /**
     * Enable result caching for frequently accessed data
     */
    public $enableResultCache = true;

    /**
     * Cache configuration for different data types
     */
    public $cacheConfig = [
        'user_sessions' => [
            'ttl' => 1800, // 30 minutes
            'prefix' => 'session_'
        ],
        'user_data' => [
            'ttl' => 3600, // 1 hour
            'prefix' => 'user_'
        ],
        'patient_data' => [
            'ttl' => 7200, // 2 hours
            'prefix' => 'patient_'
        ],
        'doctor_data' => [
            'ttl' => 7200, // 2 hours
            'prefix' => 'doctor_'
        ],
        'appointment_data' => [
            'ttl' => 1800, // 30 minutes
            'prefix' => 'appointment_'
        ],
        'medicine_data' => [
            'ttl' => 3600, // 1 hour
            'prefix' => 'medicine_'
        ],
        'dashboard_stats' => [
            'ttl' => 300, // 5 minutes
            'prefix' => 'stats_'
        ],
        'reports' => [
            'ttl' => 86400, // 24 hours
            'prefix' => 'report_'
        ]
    ];

    /**
     * Enable compression for API responses
     */
    public $enableCompression = true;

    /**
     * Compression level (1-9)
     */
    public $compressionLevel = 6;

    /**
     * Enable lazy loading for models
     */
    public $enableLazyLoading = true;

    /**
     * Batch size for bulk operations
     */
    public $batchSize = 100;

    /**
     * Enable pagination optimization
     */
    public $enablePaginationOptimization = true;

    /**
     * Maximum pagination size
     */
    public $maxPaginationSize = 100;

    /**
     * Enable query optimization
     */
    public $enableQueryOptimization = true;

    /**
     * Database indexes for performance
     */
    public $databaseIndexes = [
        'users' => ['email', 'role', 'branch_id', 'status', 'last_login'],
        'patients' => ['first_name', 'last_name', 'phone', 'email', 'branch_id', 'created_at'],
        'doctors' => ['name', 'specialization', 'branch_id', 'status'],
        'appointments' => ['patient_id', 'doctor_id', 'scheduled_at', 'status', 'branch_id'],
        'medicines' => ['name', 'category', 'branch_id', 'expiry_date', 'stock_quantity'],
        'invoices' => ['patient_id', 'status', 'created_at', 'branch_id'],
        'activity_logs' => ['user_id', 'action', 'entity_type', 'entity_id', 'created_at', 'branch_id']
    ];

    /**
     * Enable background job processing
     */
    public $enableBackgroundJobs = true;

    /**
     * Background job queue configuration
     */
    public $jobQueueConfig = [
        'max_retries' => 3,
        'retry_delay' => 60, // seconds
        'timeout' => 300, // seconds
        'max_concurrent' => 5
    ];

    /**
     * Enable memory optimization
     */
    public $enableMemoryOptimization = true;

    /**
     * Memory limit for scripts (MB)
     */
    public $memoryLimit = 256;

    /**
     * Enable garbage collection optimization
     */
    public $enableGarbageCollection = true;

    /**
     * Garbage collection probability (0-100)
     */
    public $garbageCollectionProbability = 10;

    /**
     * Enable session optimization
     */
    public $enableSessionOptimization = true;

    /**
     * Session garbage collection probability
     */
    public $sessionGarbageCollectionProbability = 1;

    /**
     * Enable asset optimization
     */
    public $enableAssetOptimization = true;

    /**
     * Asset minification
     */
    public $enableAssetMinification = true;

    /**
     * Asset bundling
     */
    public $enableAssetBundling = true;

    /**
     * CDN configuration
     */
    public $cdnConfig = [
        'enabled' => false,
        'base_url' => '',
        'assets_url' => ''
    ];

    /**
     * Enable API rate limiting
     */
    public $enableRateLimiting = true;

    /**
     * Rate limit configuration
     */
    public $rateLimitConfig = [
        'requests_per_minute' => 100,
        'requests_per_hour' => 1000,
        'requests_per_day' => 10000,
        'whitelist' => [],
        'blacklist' => []
    ];

    /**
     * Enable database query caching
     */
    public $enableQueryCache = true;

    /**
     * Query cache configuration
     */
    public $queryCacheConfig = [
        'ttl' => 300, // 5 minutes
        'max_size' => 1000, // maximum number of cached queries
        'exclude_patterns' => [
            'INSERT',
            'UPDATE',
            'DELETE',
            'DROP',
            'CREATE',
            'ALTER'
        ]
    ];

    /**
     * Enable performance profiling
     */
    public $enableProfiling = false;

    /**
     * Profiling configuration
     */
    public $profilingConfig = [
        'include_queries' => true,
        'include_memory' => true,
        'include_time' => true,
        'include_files' => false,
        'max_queries' => 100
    ];

    /**
     * Enable health monitoring
     */
    public $enableHealthMonitoring = true;

    /**
     * Health check configuration
     */
    public $healthCheckConfig = [
        'database_check' => true,
        'cache_check' => true,
        'disk_space_check' => true,
        'memory_check' => true,
        'response_time_check' => true,
        'threshold_response_time' => 2000 // milliseconds
    ];
}
