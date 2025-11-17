<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicationReminder;
use App\Models\MedicationIntake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MedicationReminderController extends Controller
{
    /**
     * Get user's medication reminders
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $reminders = MedicationReminder::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('reminder_times')
            ->get();

        return response()->json([
            'reminders' => $reminders,
        ]);
    }

    /**
     * Create medication reminder
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|in:once_daily,twice_daily,three_times_daily,four_times_daily,as_needed,custom',
            'reminder_times' => 'required|array',
            'reminder_times.*' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'duration_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'prescription_id' => 'nullable|exists:prescriptions,id',
        ]);

        $user = $request->user();

        $endDate = $request->end_date;
        if (!$endDate && $request->duration_days) {
            $endDate = Carbon::parse($request->start_date)->addDays($request->duration_days);
        }

        $reminder = MedicationReminder::create([
            'user_id' => $user->id,
            'prescription_id' => $request->prescription_id,
            'medication_name' => $request->medication_name,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'reminder_times' => $request->reminder_times,
            'start_date' => $request->start_date,
            'end_date' => $endDate,
            'notes' => $request->notes,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Rappel créé avec succès',
            'reminder' => $reminder,
        ], 201);
    }

    /**
     * Update medication reminder
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'medication_name' => 'string|max:255',
            'dosage' => 'string|max:100',
            'frequency' => 'in:once_daily,twice_daily,three_times_daily,four_times_daily,as_needed,custom',
            'reminder_times' => 'array',
            'reminder_times.*' => 'string',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $user = $request->user();
        $reminder = MedicationReminder::where('user_id', $user->id)->findOrFail($id);

        $reminder->update($request->all());

        return response()->json([
            'message' => 'Rappel mis à jour',
            'reminder' => $reminder,
        ]);
    }

    /**
     * Delete medication reminder
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $reminder = MedicationReminder::where('user_id', $user->id)->findOrFail($id);

        $reminder->delete();

        return response()->json([
            'message' => 'Rappel supprimé',
        ]);
    }

    /**
     * Record medication intake
     */
    public function recordIntake(Request $request): JsonResponse
    {
        $request->validate([
            'reminder_id' => 'required|exists:medication_reminders,id',
            'taken_at' => 'required|date',
            'skipped' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $reminder = MedicationReminder::where('user_id', $user->id)
            ->findOrFail($request->reminder_id);

        $intake = MedicationIntake::create([
            'reminder_id' => $reminder->id,
            'user_id' => $user->id,
            'taken_at' => $request->taken_at,
            'skipped' => $request->get('skipped', false),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Prise enregistrée',
            'intake' => $intake,
        ], 201);
    }

    /**
     * Get intake history
     */
    public function getIntakeHistory(Request $request, int $reminderId): JsonResponse
    {
        $user = $request->user();
        $reminder = MedicationReminder::where('user_id', $user->id)->findOrFail($reminderId);

        $intakes = MedicationIntake::where('reminder_id', $reminder->id)
            ->orderBy('taken_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'intakes' => $intakes,
            'reminder' => $reminder,
        ]);
    }

    /**
     * Get adherence statistics
     */
    public function getAdherenceStats(Request $request, int $reminderId): JsonResponse
    {
        $user = $request->user();
        $reminder = MedicationReminder::where('user_id', $user->id)->findOrFail($reminderId);

        $totalExpected = $this->calculateExpectedIntakes($reminder);
        $totalRecorded = MedicationIntake::where('reminder_id', $reminder->id)
            ->where('skipped', false)
            ->count();

        $adherenceRate = $totalExpected > 0 ? ($totalRecorded / $totalExpected) * 100 : 0;

        $last7Days = MedicationIntake::where('reminder_id', $reminder->id)
            ->where('taken_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $last30Days = MedicationIntake::where('reminder_id', $reminder->id)
            ->where('taken_at', '>=', Carbon::now()->subDays(30))
            ->count();

        return response()->json([
            'adherence_rate' => round($adherenceRate, 1),
            'total_expected' => $totalExpected,
            'total_recorded' => $totalRecorded,
            'last_7_days' => $last7Days,
            'last_30_days' => $last30Days,
        ]);
    }

    /**
     * Calculate expected intakes
     */
    private function calculateExpectedIntakes(MedicationReminder $reminder): int
    {
        $start = Carbon::parse($reminder->start_date);
        $end = $reminder->end_date ? Carbon::parse($reminder->end_date) : Carbon::now();

        $days = $start->diffInDays($end) + 1;
        $timesPerDay = count($reminder->reminder_times);

        return $days * $timesPerDay;
    }
}
