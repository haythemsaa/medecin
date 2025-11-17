<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalendarSyncService
{
    /**
     * Sync appointment to Google Calendar
     */
    public function syncToGoogleCalendar(Appointment $appointment, User $user): ?string
    {
        if (!$user->google_calendar_token) {
            return null;
        }

        try {
            $event = $this->formatAppointmentForGoogle($appointment);

            $response = Http::withToken($user->google_calendar_token)
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $event);

            if ($response->successful()) {
                $eventId = $response->json('id');
                Log::info('Appointment synced to Google Calendar', [
                    'appointment_id' => $appointment->id,
                    'google_event_id' => $eventId,
                ]);
                return $eventId;
            }

            Log::error('Failed to sync to Google Calendar', [
                'appointment_id' => $appointment->id,
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing to Google Calendar', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Sync appointment to Outlook Calendar
     */
    public function syncToOutlookCalendar(Appointment $appointment, User $user): ?string
    {
        if (!$user->outlook_calendar_token) {
            return null;
        }

        try {
            $event = $this->formatAppointmentForOutlook($appointment);

            $response = Http::withToken($user->outlook_calendar_token)
                ->post('https://graph.microsoft.com/v1.0/me/events', $event);

            if ($response->successful()) {
                $eventId = $response->json('id');
                Log::info('Appointment synced to Outlook Calendar', [
                    'appointment_id' => $appointment->id,
                    'outlook_event_id' => $eventId,
                ]);
                return $eventId;
            }

            Log::error('Failed to sync to Outlook Calendar', [
                'appointment_id' => $appointment->id,
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing to Outlook Calendar', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Delete event from Google Calendar
     */
    public function deleteFromGoogleCalendar(string $eventId, User $user): bool
    {
        if (!$user->google_calendar_token || !$eventId) {
            return false;
        }

        try {
            $response = Http::withToken($user->google_calendar_token)
                ->delete("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Error deleting from Google Calendar', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Delete event from Outlook Calendar
     */
    public function deleteFromOutlookCalendar(string $eventId, User $user): bool
    {
        if (!$user->outlook_calendar_token || !$eventId) {
            return false;
        }

        try {
            $response = Http::withToken($user->outlook_calendar_token)
                ->delete("https://graph.microsoft.com/v1.0/me/events/{$eventId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Error deleting from Outlook Calendar', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Format appointment for Google Calendar
     */
    private function formatAppointmentForGoogle(Appointment $appointment): array
    {
        $start = Carbon::parse("{$appointment->date} {$appointment->heure}");
        $end = $start->copy()->addMinutes(30);

        return [
            'summary' => "Consultation médicale",
            'description' => "Consultation avec Dr. {$appointment->medecin->user->nom} {$appointment->medecin->user->prenom}\nSpécialité: {$appointment->medecin->specialite}",
            'start' => [
                'dateTime' => $start->toIso8601String(),
                'timeZone' => 'Africa/Tunis',
            ],
            'end' => [
                'dateTime' => $end->toIso8601String(),
                'timeZone' => 'Africa/Tunis',
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 60],
                    ['method' => 'email', 'minutes' => 1440], // 24 hours
                ],
            ],
        ];
    }

    /**
     * Format appointment for Outlook Calendar
     */
    private function formatAppointmentForOutlook(Appointment $appointment): array
    {
        $start = Carbon::parse("{$appointment->date} {$appointment->heure}");
        $end = $start->copy()->addMinutes(30);

        return [
            'subject' => "Consultation médicale",
            'body' => [
                'contentType' => 'HTML',
                'content' => "Consultation avec Dr. {$appointment->medecin->user->nom} {$appointment->medecin->user->prenom}<br>Spécialité: {$appointment->medecin->specialite}",
            ],
            'start' => [
                'dateTime' => $start->toIso8601String(),
                'timeZone' => 'Africa/Tunis',
            ],
            'end' => [
                'dateTime' => $end->toIso8601String(),
                'timeZone' => 'Africa/Tunis',
            ],
            'reminderMinutesBeforeStart' => 60,
            'isReminderOn' => true,
        ];
    }

    /**
     * Get Google Calendar OAuth URL
     */
    public function getGoogleAuthUrl(): string
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect_uri');
        $scope = 'https://www.googleapis.com/auth/calendar';

        return "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);
    }

    /**
     * Get Outlook Calendar OAuth URL
     */
    public function getOutlookAuthUrl(): string
    {
        $clientId = config('services.microsoft.client_id');
        $redirectUri = config('services.microsoft.redirect_uri');
        $scope = 'Calendars.ReadWrite offline_access';

        return "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
        ]);
    }

    /**
     * Exchange Google auth code for token
     */
    public function exchangeGoogleCode(string $code): ?array
    {
        try {
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect_uri'),
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Error exchanging Google code', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Exchange Outlook auth code for token
     */
    public function exchangeOutlookCode(string $code): ?array
    {
        try {
            $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'redirect_uri' => config('services.microsoft.redirect_uri'),
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Error exchanging Outlook code', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
