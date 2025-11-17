<?php

namespace App\Jobs;

use App\Models\MedicationReminder;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMedicationReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notificationService): void
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        // Get active reminders that have a reminder time within the next 5 minutes
        $reminders = MedicationReminder::where('is_active', true)
            ->where('start_date', '<=', $now->toDateString())
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now->toDateString());
            })
            ->with('user.notificationPreference')
            ->get();

        foreach ($reminders as $reminder) {
            foreach ($reminder->reminder_times as $time) {
                // Check if this time matches current time (within 5 minute window)
                $reminderTime = Carbon::createFromFormat('H:i', $time);
                $currentTimeCarbon = Carbon::createFromFormat('H:i', $currentTime);

                if (abs($reminderTime->diffInMinutes($currentTimeCarbon)) <= 5) {
                    $this->sendReminder($reminder, $notificationService);
                    break; // Only send one notification per reminder
                }
            }
        }

        Log::info('Medication reminders sent', [
            'time' => $currentTime,
            'reminders_checked' => $reminders->count(),
        ]);
    }

    private function sendReminder(MedicationReminder $reminder, NotificationService $notificationService): void
    {
        $user = $reminder->user;
        $preference = $user->notificationPreference;

        if (!$preference) {
            return;
        }

        $message = "Rappel: Il est temps de prendre {$reminder->medication_name} ({$reminder->dosage})";

        // Send email notification
        if ($preference->email_enabled) {
            $notificationService->sendEmail(
                $user->email,
                'Rappel de médicament',
                'emails.medication-reminder',
                [
                    'user' => $user,
                    'reminder' => $reminder,
                ]
            );
        }

        // Send SMS notification
        if ($preference->sms_enabled && $user->telephone) {
            $notificationService->sendSMS(
                $user->telephone,
                $message
            );
        }

        Log::info('Medication reminder sent', [
            'user_id' => $user->id,
            'medication' => $reminder->medication_name,
            'time' => Carbon::now()->format('H:i'),
        ]);
    }
}
