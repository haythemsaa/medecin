<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Appointment $appointment;
    public string $timing; // '24h' or '1h'

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment, string $timing = '24h')
    {
        $this->appointment = $appointment;
        $this->timing = $timing;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        // Only send if appointment is still confirmed
        if ($this->appointment->status !== 'confirmed') {
            return;
        }

        $notificationService->sendAppointmentReminder($this->appointment, $this->timing);
    }
}
