<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\CacheService;

class ReviewObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->invalidateReviewCaches($review);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        $this->invalidateReviewCaches($review);
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $this->invalidateReviewCaches($review);
    }

    /**
     * Invalidate all caches related to this review
     */
    protected function invalidateReviewCaches(Review $review): void
    {
        // Invalidate reviews cache for the medecin
        $this->cacheService->invalidateReviews($review->medecin_id);

        // Invalidate medecin profile cache (includes rating)
        $this->cacheService->invalidateMedecin($review->medecin_id);

        // Invalidate analytics cache (satisfaction metrics)
        $this->cacheService->invalidateAnalytics($review->medecin_id);
    }
}
