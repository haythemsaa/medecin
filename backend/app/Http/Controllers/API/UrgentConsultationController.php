<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Medecin;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrgentConsultationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Get available doctors for urgent consultation
     */
    public function getAvailableDoctors(Request $request): JsonResponse
    {
        $request->validate([
            'specialty' => 'nullable|string',
            'max_price' => 'nullable|numeric',
        ]);

        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();

        // Get doctors who accept urgent consultations
        $query = Medecin::with(['user', 'reviews'])
            ->where('accepts_urgent', true)
            ->where('status', 'validated');

        // Filter by specialty if provided
        if ($request->specialty) {
            $query->where('specialite', $request->specialty);
        }

        $medecins = $query->get()->filter(function ($medecin) use ($now, $oneHourLater, $request) {
            // Check if doctor is available in the next hour
            $isAvailable = $this->isDoctorAvailableNow($medecin, $now, $oneHourLater);

            if (!$isAvailable) {
                return false;
            }

            // Calculate urgent price
            $urgentPrice = $medecin->tarif * (1 + $medecin->urgent_surcharge_percentage / 100);

            // Filter by max price if provided
            if ($request->max_price && $urgentPrice > $request->max_price) {
                return false;
            }

            // Add calculated fields
            $medecin->urgent_price = round($urgentPrice, 2);
            $medecin->available_in_minutes = $this->getAvailabilityTime($medecin, $now);

            return true;
        })->values();

        // Sort by availability time (soonest first)
        $medecins = $medecins->sortBy('available_in_minutes')->values();

        return response()->json([
            'doctors' => $medecins->map(fn($m) => [
                'id' => $m->id,
                'user' => [
                    'nom' => $m->user->nom,
                    'prenom' => $m->user->prenom,
                    'photo' => $m->user->photo,
                ],
                'specialite' => $m->specialite,
                'tarif' => $m->tarif,
                'urgent_price' => $m->urgent_price,
                'urgent_response_time' => $m->urgent_response_time,
                'available_in_minutes' => $m->available_in_minutes,
                'rating' => $m->reviews->avg('note') ?? 0,
                'total_reviews' => $m->reviews->count(),
            ]),
            'total' => $medecins->count(),
        ]);
    }

    /**
     * Request urgent consultation
     */
    public function requestUrgent(Request $request): JsonResponse
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'motif' => 'required|string|max:500',
            'symptoms_description' => 'nullable|string|max:1000',
        ]);

        $patient = $request->user()->patient;
        if (!$patient) {
            return response()->json([
                'message' => 'Patient profile not found',
            ], 404);
        }

        $medecin = Medecin::findOrFail($request->medecin_id);

        if (!$medecin->accepts_urgent) {
            return response()->json([
                'message' => 'Ce médecin n\'accepte pas les consultations urgentes',
            ], 400);
        }

        // Check if doctor is available
        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();

        if (!$this->isDoctorAvailableNow($medecin, $now, $oneHourLater)) {
            return response()->json([
                'message' => 'Ce médecin n\'est plus disponible pour une consultation urgente',
            ], 400);
        }

        // Calculate urgent fee
        $urgentFee = $medecin->tarif * (1 + $medecin->urgent_surcharge_percentage / 100);

        // Create appointment with earliest available slot
        $appointmentTime = $this->getEarliestSlot($medecin, $now);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'date' => $appointmentTime->toDateString(),
            'heure' => $appointmentTime->format('H:i:s'),
            'motif' => $request->motif,
            'status' => 'pending',
            'is_urgent' => true,
            'urgent_fee' => $urgentFee,
            'urgent_requested_at' => now(),
        ]);

        // Send notifications
        $this->notifyUrgentRequest($appointment, $request->symptoms_description);

        return response()->json([
            'message' => 'Demande de consultation urgente envoyée',
            'appointment' => $appointment->load(['patient.user', 'medecin.user']),
        ], 201);
    }

    /**
     * Doctor accepts urgent consultation
     */
    public function acceptUrgent(Request $request, int $appointmentId): JsonResponse
    {
        $medecin = $request->user()->medecin;
        if (!$medecin) {
            return response()->json(['message' => 'Medecin profile not found'], 404);
        }

        $appointment = Appointment::where('id', $appointmentId)
            ->where('medecin_id', $medecin->id)
            ->where('is_urgent', true)
            ->where('status', 'pending')
            ->firstOrFail();

        $appointment->update(['status' => 'confirmed']);

        // Send confirmation notification
        $this->notificationService->sendEmail(
            $appointment->patient->user->email,
            'Consultation urgente confirmée',
            'emails.urgent-consultation-confirmed',
            ['appointment' => $appointment]
        );

        return response()->json([
            'message' => 'Consultation urgente confirmée',
            'appointment' => $appointment->load(['patient.user', 'medecin.user']),
        ]);
    }

    /**
     * Get urgent consultation statistics (for doctors)
     */
    public function getStats(Request $request): JsonResponse
    {
        $medecin = $request->user()->medecin;
        if (!$medecin) {
            return response()->json(['message' => 'Medecin profile not found'], 404);
        }

        $urgentAppointments = Appointment::where('medecin_id', $medecin->id)
            ->where('is_urgent', true)
            ->get();

        $stats = [
            'total_urgent' => $urgentAppointments->count(),
            'completed' => $urgentAppointments->where('status', 'completed')->count(),
            'pending' => $urgentAppointments->where('status', 'pending')->count(),
            'cancelled' => $urgentAppointments->where('status', 'cancelled')->count(),
            'total_revenue' => $urgentAppointments->where('status', 'completed')->sum('urgent_fee'),
            'average_response_time' => $this->calculateAverageResponseTime($urgentAppointments),
        ];

        return response()->json($stats);
    }

    /**
     * Toggle urgent consultation availability
     */
    public function toggleAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'accepts_urgent' => 'required|boolean',
            'urgent_surcharge_percentage' => 'nullable|numeric|min:0|max:200',
            'urgent_response_time' => 'nullable|integer|min:15|max:120',
        ]);

        $medecin = $request->user()->medecin;
        if (!$medecin) {
            return response()->json(['message' => 'Medecin profile not found'], 404);
        }

        $medecin->update([
            'accepts_urgent' => $request->accepts_urgent,
            'urgent_surcharge_percentage' => $request->urgent_surcharge_percentage ?? $medecin->urgent_surcharge_percentage,
            'urgent_response_time' => $request->urgent_response_time ?? $medecin->urgent_response_time,
        ]);

        return response()->json([
            'message' => 'Paramètres de consultation urgente mis à jour',
            'medecin' => $medecin,
        ]);
    }

    /**
     * Check if doctor is available now
     */
    private function isDoctorAvailableNow(Medecin $medecin, Carbon $from, Carbon $to): bool
    {
        // Check if doctor has any availability in the time range
        $dayOfWeek = strtolower($from->locale('en')->dayName);

        $availability = DB::table('availabilities')
            ->where('medecin_id', $medecin->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$availability) {
            return false;
        }

        // Check if current time falls within availability hours
        $currentTime = $from->format('H:i:s');
        if ($currentTime < $availability->start_time || $currentTime > $availability->end_time) {
            return false;
        }

        // Check if doctor has no conflicting appointments
        $hasConflict = Appointment::where('medecin_id', $medecin->id)
            ->where('date', $from->toDateString())
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('heure', [$from->format('H:i:s'), $to->format('H:i:s')]);
            })
            ->exists();

        return !$hasConflict;
    }

    /**
     * Get earliest available slot for doctor
     */
    private function getEarliestSlot(Medecin $medecin, Carbon $from): Carbon
    {
        // Round up to next 15-minute interval
        $minutes = $from->minute;
        $roundedMinutes = ceil($minutes / 15) * 15;
        $slot = $from->copy()->minute($roundedMinutes)->second(0);

        // Find first available 30-minute slot
        for ($i = 0; $i < 4; $i++) { // Check next hour in 15-min increments
            $slotEnd = $slot->copy()->addMinutes(30);

            $hasConflict = Appointment::where('medecin_id', $medecin->id)
                ->where('date', $slot->toDateString())
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($slot, $slotEnd) {
                    $query->whereBetween('heure', [
                        $slot->format('H:i:s'),
                        $slotEnd->format('H:i:s')
                    ]);
                })
                ->exists();

            if (!$hasConflict) {
                return $slot;
            }

            $slot->addMinutes(15);
        }

        return $slot;
    }

    /**
     * Get availability time in minutes
     */
    private function getAvailabilityTime(Medecin $medecin, Carbon $now): int
    {
        $earliestSlot = $this->getEarliestSlot($medecin, $now);
        return $now->diffInMinutes($earliestSlot);
    }

    /**
     * Calculate average response time
     */
    private function calculateAverageResponseTime($appointments): int
    {
        $responseTimes = [];

        foreach ($appointments as $appointment) {
            if ($appointment->urgent_requested_at && $appointment->updated_at) {
                $responseTimes[] = Carbon::parse($appointment->urgent_requested_at)
                    ->diffInMinutes($appointment->updated_at);
            }
        }

        return empty($responseTimes) ? 0 : (int) (array_sum($responseTimes) / count($responseTimes));
    }

    /**
     * Send urgent request notifications
     */
    private function notifyUrgentRequest(Appointment $appointment, ?string $symptoms): void
    {
        $medecin = $appointment->medecin;
        $patient = $appointment->patient;

        // Email to doctor
        $this->notificationService->sendEmail(
            $medecin->user->email,
            '🚨 Nouvelle demande de consultation urgente',
            'emails.urgent-consultation-request',
            [
                'appointment' => $appointment,
                'symptoms' => $symptoms,
            ]
        );

        // SMS to doctor
        if ($medecin->user->telephone) {
            $this->notificationService->sendSMS(
                $medecin->user->telephone,
                "🚨 Consultation urgente demandée par {$patient->user->prenom} {$patient->user->nom}. Rendez-vous: {$appointment->date} à {$appointment->heure}. Veuillez confirmer rapidement."
            );
        }
    }
}
