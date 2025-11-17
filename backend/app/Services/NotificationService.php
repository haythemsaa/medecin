<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send SMS notification
     */
    public function sendSms(string $phone, string $message): bool
    {
        try {
            // TODO: Integrate with Tunisian SMS provider (Tunisie Telecom, Ooredoo, Orange)
            // For now, just log the SMS
            Log::info('SMS sent', ['phone' => $phone, 'message' => $message]);

            // Example integration:
            // $apiKey = config('services.sms.api_key');
            // $apiUrl = config('services.sms.api_url');
            //
            // Http::post($apiUrl, [
            //     'api_key' => $apiKey,
            //     'phone' => $phone,
            //     'message' => $message,
            //     'sender' => 'SehaDigital'
            // ]);

            return true;
        } catch (\Exception $e) {
            Log::error('SMS sending failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send email notification
     */
    public function sendEmail(string $email, string $subject, string $view, array $data = []): bool
    {
        try {
            Mail::send($view, $data, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send appointment confirmation
     */
    public function sendAppointmentConfirmation(Appointment $appointment): void
    {
        $patient = $appointment->patient;
        $medecin = $appointment->medecin;

        // Send email to patient
        $this->sendEmail(
            $patient->user->email,
            'Confirmation de votre rendez-vous - Seha Digital',
            'emails.appointment-confirmation-patient',
            ['appointment' => $appointment]
        );

        // Send SMS to patient
        $this->sendSms(
            $patient->user->phone,
            "Seha Digital: Votre RDV avec Dr. {$medecin->last_name} le " .
            $appointment->appointment_date->format('d/m/Y à H:i') . " est confirmé."
        );

        // Notify doctor
        $this->sendEmail(
            $medecin->user->email,
            'Nouveau rendez-vous - Seha Digital',
            'emails.appointment-confirmation-medecin',
            ['appointment' => $appointment]
        );
    }

    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder(Appointment $appointment, string $when = '24h'): void
    {
        $patient = $appointment->patient;
        $medecin = $appointment->medecin;

        $message = "Rappel: Votre RDV avec Dr. {$medecin->last_name} est prévu " .
            ($when === '24h' ? 'demain' : 'dans 1 heure') . " à " .
            $appointment->appointment_date->format('H:i') . ". Lien: " .
            url("/appointments/{$appointment->id}");

        $this->sendSms($patient->user->phone, $message);

        $this->sendEmail(
            $patient->user->email,
            "Rappel: Votre rendez-vous {$when}",
            'emails.appointment-reminder',
            ['appointment' => $appointment, 'when' => $when]
        );
    }

    /**
     * Send appointment cancellation notification
     */
    public function sendAppointmentCancellation(Appointment $appointment): void
    {
        $patient = $appointment->patient;
        $medecin = $appointment->medecin;

        // Notify patient
        $this->sendEmail(
            $patient->user->email,
            'Annulation de votre rendez-vous - Seha Digital',
            'emails.appointment-cancelled',
            ['appointment' => $appointment]
        );

        $refundMessage = '';
        if ($appointment->refund_amount > 0) {
            $refundMessage = " Remboursement: {$appointment->refund_amount} TND.";
        }

        $this->sendSms(
            $patient->user->phone,
            "Votre RDV avec Dr. {$medecin->last_name} du " .
            $appointment->appointment_date->format('d/m/Y') . " a été annulé.{$refundMessage}"
        );

        // Notify doctor
        $this->sendEmail(
            $medecin->user->email,
            'Rendez-vous annulé - Seha Digital',
            'emails.appointment-cancelled-medecin',
            ['appointment' => $appointment]
        );
    }

    /**
     * Send welcome email to new patient
     */
    public function sendWelcomePatient(User $user): void
    {
        $this->sendEmail(
            $user->email,
            'Bienvenue sur Seha Digital',
            'emails.welcome-patient',
            ['user' => $user]
        );
    }

    /**
     * Send validation notification to medecin
     */
    public function sendMedecinValidation(User $user, bool $validated): void
    {
        $subject = $validated
            ? 'Votre compte médecin a été validé - Seha Digital'
            : 'Information sur votre inscription - Seha Digital';

        $this->sendEmail(
            $user->email,
            $subject,
            $validated ? 'emails.medecin-validated' : 'emails.medecin-rejected',
            ['user' => $user]
        );

        if ($validated) {
            $this->sendSms(
                $user->phone,
                "Félicitations! Votre compte Seha Digital a été validé. Vous pouvez maintenant recevoir des patients."
            );
        }
    }

    /**
     * Send prescription notification
     */
    public function sendPrescriptionNotification(User $patient, string $pdfPath): void
    {
        $this->sendEmail(
            $patient->email,
            'Votre ordonnance est disponible - Seha Digital',
            'emails.prescription-ready',
            ['patient' => $patient, 'pdfPath' => $pdfPath]
        );
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation(User $user, $payment): void
    {
        $this->sendEmail(
            $user->email,
            'Confirmation de paiement - Seha Digital',
            'emails.payment-confirmation',
            ['user' => $user, 'payment' => $payment]
        );
    }
}
