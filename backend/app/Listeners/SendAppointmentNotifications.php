<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(AppointmentCreated $event): void
    {
        $appointment = $event->appointment->load(['patient.user', 'medecin.user']);

        // Send confirmation to patient and doctor
        $this->notificationService->sendAppointmentConfirmation($appointment);
    }

    /**
     * Handle a job failure.
     */
    public function failed(AppointmentCreated $event, \Throwable $exception): void
    {
        // Log the failure
        \Log::error('Failed to send appointment notifications', [
            'appointment_id' => $event->appointment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
