<?php

namespace App\Http\Middleware;

use App\Services\CacheService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Generate cache key based on URL and query parameters
        $cacheKey = $this->generateCacheKey($request);

        // Try to get cached response
        $cachedResponse = $this->cacheService->get($cacheKey);

        if ($cachedResponse !== null) {
            return response()->json($cachedResponse)
                ->header('X-Cache', 'HIT');
        }

        // Process request
        $response = $next($request);

        // Cache successful JSON responses
        if ($response->isSuccessful() && $response->headers->get('Content-Type') === 'application/json') {
            $content = json_decode($response->getContent(), true);
            $this->cacheService->put($cacheKey, $content, $ttl);
        }

        return $response->header('X-Cache', 'MISS');
    }

    /**
     * Generate cache key from request
     */
    protected function generateCacheKey(Request $request): string
    {
        $url = $request->url();
        $queryParams = $request->query();
        $userId = $request->user()?->id ?? 'guest';

        ksort($queryParams);

        $key = sprintf(
            'response:%s:%s:%s',
            $userId,
            md5($url),
            md5(json_encode($queryParams))
        );

        return $key;
    }
}
