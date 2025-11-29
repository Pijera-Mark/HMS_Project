<?php

namespace App\Services;

/**
 * Cache Service - Centralized caching logic
 * Eliminates redundancy across controllers and services
 */
class CacheService
{
    protected $cache;
    protected $defaultTTL = 3600; // 1 hour

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    /**
     * Get cached data
     */
    public function get(string $key, $default = null)
    {
        return $this->cache->get($key, $default);
    }

    /**
     * Set cache data
     */
    public function set(string $key, $value, int $ttl = null): bool
    {
        return $this->cache->save($key, $value, $ttl ?? $this->defaultTTL);
    }

    /**
     * Delete cache data
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    /**
     * Clear all cache
     */
    public function clear(): bool
    {
        return $this->cache->clean();
    }

    /**
     * Get user profile cache key
     */
    public function getUserProfileKey(int $userId): string
    {
        return "user_profile_{$userId}";
    }

    /**
     * Get user statistics cache key
     */
    public function getUserStatsKey(int $userId): string
    {
        return "user_stats_{$userId}";
    }

    /**
     * Get dashboard cache key
     */
    public function getDashboardKey(int $userId, string $role): string
    {
        return "dashboard_{$role}_{$userId}";
    }

    /**
     * Get appointment cache key
     */
    public function getAppointmentKey(string $date, int $doctorId = null): string
    {
        $key = "appointments_{$date}";
        if ($doctorId) {
            $key .= "_doctor_{$doctorId}";
        }
        return $key;
    }

    /**
     * Cache user profile
     */
    public function cacheUserProfile(int $userId, array $profile): bool
    {
        return $this->set($this->getUserProfileKey($userId), $profile);
    }

    /**
     * Get cached user profile
     */
    public function getCachedUserProfile(int $userId): ?array
    {
        return $this->get($this->getUserProfileKey($userId));
    }

    /**
     * Cache user statistics
     */
    public function cacheUserStats(int $userId, array $stats): bool
    {
        return $this->set($this->getUserStatsKey($userId), $stats, 1800); // 30 minutes
    }

    /**
     * Get cached user statistics
     */
    public function getCachedUserStats(int $userId): ?array
    {
        return $this->get($this->getUserStatsKey($userId));
    }

    /**
     * Cache dashboard data
     */
    public function cacheDashboard(int $userId, string $role, array $data): bool
    {
        return $this->set($this->getDashboardKey($userId, $role), $data, 900); // 15 minutes
    }

    /**
     * Get cached dashboard data
     */
    public function getCachedDashboard(int $userId, string $role): ?array
    {
        return $this->get($this->getDashboardKey($userId, $role));
    }

    /**
     * Invalidate user cache
     */
    public function invalidateUserCache(int $userId): void
    {
        $this->delete($this->getUserProfileKey($userId));
        $this->delete($this->getUserStatsKey($userId));
        
        // Invalidate dashboard caches for all roles
        $roles = ['admin', 'doctor', 'patient', 'receptionist', 'it_staff'];
        foreach ($roles as $role) {
            $this->delete($this->getDashboardKey($userId, $role));
        }
    }

    /**
     * Invalidate appointment cache
     */
    public function invalidateAppointmentCache(string $date, int $doctorId = null): void
    {
        $this->delete($this->getAppointmentKey($date));
        if ($doctorId) {
            $this->delete($this->getAppointmentKey($date, $doctorId));
        }
    }

    /**
     * Remember pattern - get from cache or execute callback
     */
    public function remember(string $key, callable $callback, int $ttl = null)
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    /**
     * Forget pattern - delete cache and optionally execute callback
     */
    public function forget(string $key, callable $callback = null)
    {
        $this->delete($key);
        
        if ($callback) {
            return $callback();
        }
        
        return null;
    }

    /**
     * Tag-based cache invalidation
     */
    public function tag(array $tags): self
    {
        // This would require a more advanced cache implementation
        // For now, we'll use the basic cache
        return $this;
    }

    /**
     * Flush cache by tags
     */
    public function flushTags(array $tags): bool
    {
        // This would require a more advanced cache implementation
        // For now, we'll clear all cache
        return $this->clear();
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        // This would require cache driver that supports statistics
        return [
            'hits' => 0,
            'misses' => 0,
            'memory_usage' => 0,
            'items_count' => 0
        ];
    }

    /**
     * Warm up cache for user
     */
    public function warmUpUserCache(int $userId): void
    {
        // This would be called during login or profile access
        // Implementation would depend on specific requirements
    }

    /**
     * Preload common data
     */
    public function preloadCommonData(): void
    {
        // Preload frequently accessed data
        // Implementation would depend on specific requirements
    }
}
