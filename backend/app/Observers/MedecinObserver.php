<?php

namespace App\Observers;

use App\Models\Medecin;
use App\Services\CacheService;

class MedecinObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Medecin "created" event.
     */
    public function created(Medecin $medecin): void
    {
        $this->invalidateMedecinCaches($medecin);
        $this->cacheService->warmUpMedecinCache($medecin->id);
    }

    /**
     * Handle the Medecin "updated" event.
     */
    public function updated(Medecin $medecin): void
    {
        $this->invalidateMedecinCaches($medecin);
        $this->cacheService->warmUpMedecinCache($medecin->id);
    }

    /**
     * Handle the Medecin "deleted" event.
     */
    public function deleted(Medecin $medecin): void
    {
        $this->invalidateMedecinCaches($medecin);
    }

    /**
     * Invalidate all caches related to this medecin
     */
    protected function invalidateMedecinCaches(Medecin $medecin): void
    {
        // Invalidate medecin profile cache
        $this->cacheService->invalidateMedecin($medecin->id);

        // Invalidate analytics cache
        $this->cacheService->invalidateAnalytics($medecin->id);

        // Invalidate availability cache
        $this->cacheService->invalidateAvailability($medecin->id);

        // Invalidate reviews cache
        $this->cacheService->invalidateReviews($medecin->id);

        // Invalidate search caches (this is broad but necessary)
        $this->cacheService->forgetByPattern('search:*');

        // Invalidate appointments cache if user exists
        if ($medecin->user) {
            $this->cacheService->invalidateAppointments($medecin->user->id, 'medecin');
        }
    }
}
