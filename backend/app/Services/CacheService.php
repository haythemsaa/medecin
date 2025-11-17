<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    // Cache TTL constants (in seconds)
    const TTL_SHORT = 300;      // 5 minutes
    const TTL_MEDIUM = 1800;    // 30 minutes
    const TTL_LONG = 3600;      // 1 hour
    const TTL_DAY = 86400;      // 24 hours

    /**
     * Get or set cached data
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Get cached data
     */
    public function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Set cached data
     */
    public function put(string $key, $value, int $ttl): bool
    {
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Delete cached data
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Delete multiple cache keys matching a pattern
     */
    public function forgetByPattern(string $pattern): void
    {
        $keys = Redis::keys($pattern);

        foreach ($keys as $key) {
            // Remove the Redis prefix from the key
            $cleanKey = str_replace(config('database.redis.options.prefix'), '', $key);
            Cache::forget($cleanKey);
        }
    }

    /**
     * Cache medecin data
     */
    public function cacheMedecin(int $medecinId, $data): bool
    {
        return $this->put("medecin:{$medecinId}", $data, self::TTL_MEDIUM);
    }

    /**
     * Get cached medecin data
     */
    public function getMedecin(int $medecinId)
    {
        return $this->get("medecin:{$medecinId}");
    }

    /**
     * Invalidate medecin cache
     */
    public function invalidateMedecin(int $medecinId): bool
    {
        return $this->forget("medecin:{$medecinId}");
    }

    /**
     * Cache search results
     */
    public function cacheSearchResults(string $queryHash, $results): bool
    {
        return $this->put("search:{$queryHash}", $results, self::TTL_SHORT);
    }

    /**
     * Get cached search results
     */
    public function getSearchResults(string $queryHash)
    {
        return $this->get("search:{$queryHash}");
    }

    /**
     * Cache analytics data
     */
    public function cacheAnalytics(int $medecinId, string $type, $data): bool
    {
        return $this->put("analytics:{$medecinId}:{$type}", $data, self::TTL_MEDIUM);
    }

    /**
     * Get cached analytics data
     */
    public function getAnalytics(int $medecinId, string $type)
    {
        return $this->get("analytics:{$medecinId}:{$type}");
    }

    /**
     * Invalidate all analytics for a medecin
     */
    public function invalidateAnalytics(int $medecinId): void
    {
        $this->forgetByPattern("analytics:{$medecinId}:*");
    }

    /**
     * Cache user session data
     */
    public function cacheUserSession(int $userId, $data): bool
    {
        return $this->put("user:session:{$userId}", $data, self::TTL_LONG);
    }

    /**
     * Get cached user session
     */
    public function getUserSession(int $userId)
    {
        return $this->get("user:session:{$userId}");
    }

    /**
     * Invalidate user session
     */
    public function invalidateUserSession(int $userId): bool
    {
        return $this->forget("user:session:{$userId}");
    }

    /**
     * Cache appointments list
     */
    public function cacheAppointments(int $userId, string $role, $appointments): bool
    {
        return $this->put("appointments:{$role}:{$userId}", $appointments, self::TTL_SHORT);
    }

    /**
     * Get cached appointments
     */
    public function getAppointments(int $userId, string $role)
    {
        return $this->get("appointments:{$role}:{$userId}");
    }

    /**
     * Invalidate appointments cache for user
     */
    public function invalidateAppointments(int $userId, string $role): bool
    {
        return $this->forget("appointments:{$role}:{$userId}");
    }

    /**
     * Invalidate all appointment caches related to an appointment
     */
    public function invalidateAppointmentCaches(int $patientUserId, int $medecinUserId): void
    {
        $this->forget("appointments:patient:{$patientUserId}");
        $this->forget("appointments:medecin:{$medecinUserId}");
    }

    /**
     * Cache availability slots
     */
    public function cacheAvailability(int $medecinId, string $date, $slots): bool
    {
        return $this->put("availability:{$medecinId}:{$date}", $slots, self::TTL_MEDIUM);
    }

    /**
     * Get cached availability
     */
    public function getAvailability(int $medecinId, string $date)
    {
        return $this->get("availability:{$medecinId}:{$date}");
    }

    /**
     * Invalidate availability cache for medecin
     */
    public function invalidateAvailability(int $medecinId): void
    {
        $this->forgetByPattern("availability:{$medecinId}:*");
    }

    /**
     * Cache reviews for medecin
     */
    public function cacheReviews(int $medecinId, $reviews): bool
    {
        return $this->put("reviews:medecin:{$medecinId}", $reviews, self::TTL_MEDIUM);
    }

    /**
     * Get cached reviews
     */
    public function getReviews(int $medecinId)
    {
        return $this->get("reviews:medecin:{$medecinId}");
    }

    /**
     * Invalidate reviews cache
     */
    public function invalidateReviews(int $medecinId): bool
    {
        return $this->forget("reviews:medecin:{$medecinId}");
    }

    /**
     * Increment a counter (useful for rate limiting)
     */
    public function increment(string $key, int $ttl = 60): int
    {
        $value = Cache::increment($key);

        if ($value === 1) {
            // First increment, set expiry
            Cache::put($key, 1, $ttl);
        }

        return $value;
    }

    /**
     * Check rate limit
     */
    public function checkRateLimit(string $identifier, int $maxAttempts, int $decaySeconds): bool
    {
        $key = "rate_limit:{$identifier}";
        $attempts = $this->get($key, 0);

        if ($attempts >= $maxAttempts) {
            return false;
        }

        $this->increment($key, $decaySeconds);
        return true;
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        try {
            $info = Redis::info();

            return [
                'connected' => true,
                'used_memory' => $info['used_memory_human'] ?? 'N/A',
                'total_keys' => $info['db0']['keys'] ?? 0,
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate(
                    $info['keyspace_hits'] ?? 0,
                    $info['keyspace_misses'] ?? 0
                ),
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate(int $hits, int $misses): string
    {
        $total = $hits + $misses;

        if ($total === 0) {
            return '0%';
        }

        $rate = ($hits / $total) * 100;
        return number_format($rate, 2) . '%';
    }

    /**
     * Flush all cache
     */
    public function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Warm up cache for a medecin
     */
    public function warmUpMedecinCache(int $medecinId): void
    {
        // This would be called after medecin data changes
        // to pre-populate the cache with fresh data

        $medecin = \App\Models\Medecin::with('user')->find($medecinId);

        if ($medecin) {
            $this->cacheMedecin($medecinId, $medecin->toArray());
        }
    }
}
