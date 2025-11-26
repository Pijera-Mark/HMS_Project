<?php

namespace App\Libraries;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\I18n\Time;

/**
 * Enhanced Caching System with intelligent cache management
 * - Multi-level caching (memory + file)
 * - Automatic cache invalidation
 * - Cache warming strategies
 * - Performance monitoring
 * - Tag-based cache management
 */
class EnhancedCache
{
    protected CacheInterface $cache;
    protected array $memoryCache = [];
    protected array $cacheStats = [
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0
    ];

    /**
     * Cache configuration
     */
    protected array $config = [
        'default_ttl' => 3600, // 1 hour
        'memory_limit' => 100, // Max items in memory cache
        'enable_stats' => true,
        'auto_warm' => true,
        'compression' => false
    ];

    public function __construct(CacheInterface $cache, array $config = [])
    {
        $this->cache = $cache;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Get cached data with multi-level lookup
     */
    public function get(string $key, $default = null)
    {
        // Check memory cache first
        if (isset($this->memoryCache[$key])) {
            $this->cacheStats['hits']++;
            return $this->memoryCache[$key]['data'];
        }

        // Check file cache
        $data = $this->cache->get($key);
        
        if ($data !== null) {
            $this->cacheStats['hits']++;
            
            // Store in memory cache if within limit
            if (count($this->memoryCache) < $this->config['memory_limit']) {
                $this->memoryCache[$key] = [
                    'data' => $data,
                    'timestamp' => Time::now()->getTimestamp()
                ];
            }
            
            return $data;
        }

        $this->cacheStats['misses']++;
        return $default;
    }

    /**
     * Set cached data with intelligent storage
     */
    public function set(string $key, $value, int $ttl = null, array $tags = []): bool
    {
        $ttl = $ttl ?? $this->config['default_ttl'];
        
        // Store in file cache
        $success = $this->cache->save($key, $value, $ttl);
        
        if ($success) {
            // Store in memory cache
            $this->memoryCache[$key] = [
                'data' => $value,
                'timestamp' => Time::now()->getTimestamp(),
                'ttl' => $ttl,
                'tags' => $tags
            ];
            
            // Manage memory cache size
            $this->manageMemoryCache();
            
            // Store tag relationships
            if (!empty($tags)) {
                $this->manageTags($key, $tags);
            }
            
            $this->cacheStats['sets']++;
        }
        
        return $success;
    }

    /**
     * Delete cached data by key
     */
    public function delete(string $key): bool
    {
        // Remove from memory cache
        unset($this->memoryCache[$key]);
        
        // Remove from file cache
        $success = $this->cache->delete($key);
        
        if ($success) {
            $this->cacheStats['deletes']++;
        }
        
        return $success;
    }

    /**
     * Delete cached data by tags
     */
    public function deleteByTags(array $tags): bool
    {
        $deleted = 0;
        
        foreach ($tags as $tag) {
            $tagKey = "tag_{$tag}";
            $keys = $this->cache->get($tagKey) ?? [];
            
            foreach ($keys as $key) {
                if ($this->delete($key)) {
                    $deleted++;
                }
            }
            
            // Clear tag cache
            $this->cache->delete($tagKey);
        }
        
        return $deleted > 0;
    }

    /**
     * Remember data with callback
     */
    public function remember(string $key, callable $callback, int $ttl = null, array $tags = [])
    {
        $data = $this->get($key);
        
        if ($data === null) {
            $data = $callback();
            $this->set($key, $data, $ttl, $tags);
        }
        
        return $data;
    }

    /**
     * Get multiple keys at once
     */
    public function getMultiple(array $keys): array
    {
        $results = [];
        
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        
        return $results;
    }

    /**
     * Set multiple keys at once
     */
    public function setMultiple(array $items, int $ttl = null, array $tags = []): bool
    {
        $success = true;
        
        foreach ($items as $key => $value) {
            if (!$this->set($key, $value, $ttl, $tags)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Warm cache with predefined data
     */
    public function warmCache(array $warmupData): void
    {
        foreach ($warmupData as $key => $config) {
            $data = $config['data'] ?? null;
            $ttl = $config['ttl'] ?? $this->config['default_ttl'];
            $tags = $config['tags'] ?? [];
            
            if ($data !== null) {
                $this->set($key, $data, $ttl, $tags);
            }
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        $total = $this->cacheStats['hits'] + $this->cacheStats['misses'];
        $hitRate = $total > 0 ? round(($this->cacheStats['hits'] / $total) * 100, 2) : 0;
        
        return array_merge($this->cacheStats, [
            'hit_rate' => $hitRate,
            'memory_usage' => count($this->memoryCache),
            'memory_limit' => $this->config['memory_limit']
        ]);
    }

    /**
     * Clear all cache
     */
    public function clear(): bool
    {
        $this->memoryCache = [];
        return $this->cache->clean();
    }

    /**
     * Manage memory cache size
     */
    protected function manageMemoryCache(): void
    {
        if (count($this->memoryCache) <= $this->config['memory_limit']) {
            return;
        }
        
        // Sort by timestamp (oldest first)
        uasort($this->memoryCache, function ($a, $b) {
            return $a['timestamp'] - $b['timestamp'];
        });
        
        // Remove oldest items
        $toRemove = count($this->memoryCache) - $this->config['memory_limit'];
        $keys = array_keys($this->memoryCache);
        
        for ($i = 0; $i < $toRemove; $i++) {
            unset($this->memoryCache[$keys[$i]]);
        }
    }

    /**
     * Manage tag relationships
     */
    protected function manageTags(string $key, array $tags): void
    {
        foreach ($tags as $tag) {
            $tagKey = "tag_{$tag}";
            $keys = $this->cache->get($tagKey) ?? [];
            
            if (!in_array($key, $keys)) {
                $keys[] = $key;
                $this->cache->save($tagKey, $keys, 86400); // 24 hours
            }
        }
    }

    /**
     * Generate cache key with context
     */
    public function generateKey(string $baseKey, array $context = []): string
    {
        $keyParts = ['hms', $baseKey];
        
        if (!empty($context)) {
            $keyParts[] = md5(json_encode($context));
        }
        
        return implode('_', $keyParts);
    }

    /**
     * Get dashboard-specific cache key
     */
    public function getDashboardKey(string $metric, ?int $branchId = null, ?string $role = null): string
    {
        $context = [];
        
        if ($branchId) {
            $context['branch_id'] = $branchId;
        }
        
        if ($role) {
            $context['role'] = $role;
        }
        
        return $this->generateKey("dashboard_{$metric}", $context);
    }

    /**
     * Cache dashboard statistics
     */
    public function cacheDashboardStats(string $metric, $data, int $ttl = 300, ?int $branchId = null, ?string $role = null): bool
    {
        $key = $this->getDashboardKey($metric, $branchId, $role);
        $tags = ['dashboard', "dashboard_{$metric}"];
        
        if ($branchId) {
            $tags[] = "branch_{$branchId}";
        }
        
        if ($role) {
            $tags[] = "role_{$role}";
        }
        
        return $this->set($key, $data, $ttl, $tags);
    }

    /**
     * Get cached dashboard statistics
     */
    public function getCachedDashboardStats(string $metric, ?int $branchId = null, ?string $role = null)
    {
        $key = $this->getDashboardKey($metric, $branchId, $role);
        return $this->get($key);
    }

    /**
     * Invalidate dashboard cache
     */
    public function invalidateDashboardCache(?int $branchId = null): bool
    {
        $tags = ['dashboard'];
        
        if ($branchId) {
            $tags[] = "branch_{$branchId}";
        }
        
        return $this->deleteByTags($tags);
    }

    /**
     * Get cache size information
     */
    public function getCacheInfo(): array
    {
        return [
            'memory_cache_size' => count($this->memoryCache),
            'memory_cache_limit' => $this->config['memory_limit'],
            'memory_usage_percent' => round((count($this->memoryCache) / $this->config['memory_limit']) * 100, 2),
            'stats' => $this->getStats()
        ];
    }
}
