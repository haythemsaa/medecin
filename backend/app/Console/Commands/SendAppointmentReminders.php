<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\NotificationPreference;
use App\Jobs\SendEmailNotification;
use App\Jobs\SendSMSNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminders for upcoming appointments';

    public function handle()
    {
        $this->info('Starting appointment reminders...');

        // Get all confirmed appointments
        $appointments = Appointment::with(['patient.user', 'medecin.user'])
            ->where('status', 'confirmed')
            ->where('date', '>=', now())
            ->where('date', '<=', now()->addDays(2))
            ->get();

        $sent = 0;

        foreach ($appointments as $appointment) {
            $appointmentDateTime = Carbon::parse($appointment->date . ' ' . $appointment->time);
            $hoursUntil = now()->diffInHours($appointmentDateTime, false);

            // Skip past appointments
            if ($hoursUntil < 0) {
                continue;
            }

            // Get patient's notification preferences
            $preferences = NotificationPreference::where('user_id', $appointment->patient->user_id)->first();

            if (!$preferences || !$preferences->appointment_reminders) {
                continue;
            }

            $reminderHours = $preferences->reminder_hours_before ?? [24, 1];

            // Check if we should send a reminder now
            foreach ($reminderHours as $hours) {
                // Send if within 15-minute window of reminder time
                if (abs($hoursUntil - $hours) <= 0.25) { // 15 minutes = 0.25 hours
                    $this->sendReminder($appointment, $preferences, $hours);
                    $sent++;
                    break;
                }
            }
        }

        $this->info("Sent {$sent} reminder(s)");
        return 0;
    }

    private function sendReminder(Appointment $appointment, NotificationPreference $preferences, int $hoursBefore)
    {
        $patient = $appointment->patient;
        $medecin = $appointment->medecin;

        $medecinName = "Dr. {$medecin->user->first_name} {$medecin->user->last_name}";
        $appointmentDate = Carbon::parse($appointment->date)->format('d/m/Y');
        $appointmentTime = $appointment->time;

        $subject = "Rappel : Rendez-vous dans {$hoursBefore}h";

        $message = "Bonjour {$patient->user->first_name},\n\n";
        $message .= "Ceci est un rappel pour votre rendez-vous :\n\n";
        $message .= "Médecin : {$medecinName}\n";
        $message .= "Date : {$appointmentDate}\n";
        $message .= "Heure : {$appointmentTime}\n";
        $message .= "Type : " . ($appointment->type === 'video' ? 'Consultation vidéo' : 'Au cabinet') . "\n\n";

        if ($appointment->type === 'video') {
            $message .= "Vous recevrez un lien de consultation 15 minutes avant l'heure prévue.\n\n";
        } else {
            $message .= "Adresse : {$medecin->adresse_cabinet}, {$medecin->ville}\n\n";
        }

        $message .= "Pour annuler ou modifier, connectez-vous à votre compte.\n\n";
        $message .= "Cordialement,\nSeha Digital";

        // Send email
        if ($preferences->email_enabled) {
            SendEmailNotification::dispatch(
                $patient->user->email,
                $subject,
                'emails.appointment-reminder',
                [
                    'patient_name' => $patient->user->first_name,
                    'medecin_name' => $medecinName,
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'appointment_type' => $appointment->type,
                    'hours_before' => $hoursBefore,
                    'medecin_address' => $medecin->adresse_cabinet . ', ' . $medecin->ville,
                ]
            );
        }

        // Send SMS
        if ($preferences->sms_enabled) {
            $smsMessage = "Rappel Seha: RDV avec {$medecinName} le {$appointmentDate} à {$appointmentTime}";
            SendSMSNotification::dispatch($patient->user->phone, $smsMessage);
        }

        $this->line("Reminder sent to {$patient->user->email} for appointment #{$appointment->id}");
    }
}
