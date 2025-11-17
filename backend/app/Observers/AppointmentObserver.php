<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\CacheService;

class AppointmentObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $this->invalidateAppointmentCaches($appointment);
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        $this->invalidateAppointmentCaches($appointment);

        // If appointment is completed, invalidate analytics
        if ($appointment->status === 'completed') {
            $this->cacheService->invalidateAnalytics($appointment->medecin_id);
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        $this->invalidateAppointmentCaches($appointment);
    }

    /**
     * Invalidate all caches related to this appointment
     */
    protected function invalidateAppointmentCaches(Appointment $appointment): void
    {
        // Invalidate appointment lists for both patient and medecin
        if ($appointment->patient && $appointment->patient->user) {
            $this->cacheService->invalidateAppointments(
                $appointment->patient->user->id,
                'patient'
            );
        }

        if ($appointment->medecin && $appointment->medecin->user) {
            $this->cacheService->invalidateAppointments(
                $appointment->medecin->user->id,
                'medecin'
            );
        }

        // Invalidate availability cache for the medecin
        $this->cacheService->invalidateAvailability($appointment->medecin_id);

        // Invalidate analytics cache
        $this->cacheService->invalidateAnalytics($appointment->medecin_id);
    }
}
