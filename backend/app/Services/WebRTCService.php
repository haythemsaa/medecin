<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Consultation;
use Illuminate\Support\Facades\Log;

class WebRTCService
{
    /**
     * Generate WebRTC room for consultation
     */
    public function createRoom(Appointment $appointment): array
    {
        $roomId = 'consultation_' . $appointment->id . '_' . uniqid();

        // TODO: Integrate with TURN/STUN server configuration
        // For Tunisian deployment, use local TURN/STUN servers

        return [
            'room_id' => $roomId,
            'ice_servers' => $this->getIceServers(),
            'token' => $this->generateRoomToken($roomId, $appointment),
        ];
    }

    /**
     * Get ICE servers configuration (STUN/TURN)
     */
    protected function getIceServers(): array
    {
        return [
            [
                'urls' => env('STUN_SERVER_URL', 'stun:stun.sehadigital.tn:3478')
            ],
            [
                'urls' => env('TURN_SERVER_URL', 'turn:turn.sehadigital.tn:3478'),
                'username' => env('TURN_USERNAME', ''),
                'credential' => env('TURN_PASSWORD', '')
            ],
            // Fallback to public STUN servers
            [
                'urls' => 'stun:stun.l.google.com:19302'
            ]
        ];
    }

    /**
     * Generate room access token
     */
    protected function generateRoomToken(string $roomId, Appointment $appointment): string
    {
        // Generate JWT token for room access
        // This token should be verified before allowing WebRTC connection

        $payload = [
            'room_id' => $roomId,
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'medecin_id' => $appointment->medecin_id,
            'expires_at' => now()->addHours(2)->timestamp
        ];

        // Simple base64 encoding for now
        // TODO: Use proper JWT library
        return base64_encode(json_encode($payload));
    }

    /**
     * Verify room access
     */
    public function verifyRoomAccess(string $token, int $userId): bool
    {
        try {
            $payload = json_decode(base64_decode($token), true);

            if (!$payload || $payload['expires_at'] < time()) {
                return false;
            }

            $appointment = Appointment::find($payload['appointment_id']);
            if (!$appointment) {
                return false;
            }

            // Check if user is participant
            return $appointment->patient->user_id === $userId ||
                   $appointment->medecin->user_id === $userId;

        } catch (\Exception $e) {
            Log::error('WebRTC room access verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Record consultation start
     */
    public function startConsultation(Appointment $appointment, int $medecinUserId): Consultation
    {
        // Update appointment status
        $appointment->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        // Create or update consultation record
        $consultation = Consultation::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'patient_id' => $appointment->patient_id,
                'medecin_id' => $appointment->medecin_id,
                'start_time' => now()
            ]
        );

        return $consultation;
    }

    /**
     * Record consultation end
     */
    public function endConsultation(Appointment $appointment): void
    {
        $consultation = $appointment->consultation;

        if ($consultation && !$consultation->end_time) {
            $duration = now()->diffInMinutes($consultation->start_time);

            $consultation->update([
                'end_time' => now(),
                'duration_minutes' => $duration
            ]);
        }

        $appointment->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Report technical issue during consultation
     */
    public function reportTechnicalIssue(Consultation $consultation, string $description): void
    {
        $consultation->update([
            'technical_issues' => true,
            'technical_issues_description' => $description,
            'connection_quality' => 'poor'
        ]);

        Log::warning('Technical issue reported during consultation', [
            'consultation_id' => $consultation->id,
            'description' => $description
        ]);
    }

    /**
     * Update connection quality
     */
    public function updateConnectionQuality(Consultation $consultation, string $quality): void
    {
        $consultation->update(['connection_quality' => $quality]);
    }
}
