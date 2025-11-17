<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    /**
     * Get medecin's availabilities
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $availabilities = Availability::where('medecin_id', $user->medecin->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($availabilities);
    }

    /**
     * Get medecin's availabilities by medecin ID (public)
     */
    public function getByMedecin(Request $request, $medecinId)
    {
        $availabilities = Availability::where('medecin_id', $medecinId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($availabilities);
    }

    /**
     * Create a new availability slot
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for overlapping slots
        $overlapping = Availability::where('medecin_id', $user->medecin->id)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($overlapping) {
            return response()->json([
                'message' => 'Ce créneau chevauche un créneau existant'
            ], 422);
        }

        $availability = Availability::create([
            'medecin_id' => $user->medecin->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->input('is_active', true),
        ]);

        return response()->json($availability, 201);
    }

    /**
     * Update an availability slot
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $availability = Availability::where('medecin_id', $user->medecin->id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'day_of_week' => 'integer|between:0,6',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $availability->update($request->only([
            'day_of_week',
            'start_time',
            'end_time',
            'is_active'
        ]));

        return response()->json($availability);
    }

    /**
     * Delete an availability slot
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $availability = Availability::where('medecin_id', $user->medecin->id)
            ->findOrFail($id);

        $availability->delete();

        return response()->json(['message' => 'Availability deleted successfully']);
    }

    /**
     * Delete all availabilities for a medecin
     */
    public function destroyAll(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Availability::where('medecin_id', $user->medecin->id)->delete();

        return response()->json(['message' => 'All availabilities deleted successfully']);
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots(Request $request, $medecinId)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $date = new \DateTime($request->date);
        $dayOfWeek = (int) $date->format('w');

        // Get availabilities for this day
        $availabilities = Availability::where('medecin_id', $medecinId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        // Get existing appointments for this date
        $appointments = \App\Models\Appointment::where('medecin_id', $medecinId)
            ->whereDate('appointment_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        // Generate available slots (30-minute intervals)
        $slots = [];
        foreach ($availabilities as $availability) {
            $start = new \DateTime($request->date . ' ' . $availability->start_time);
            $end = new \DateTime($request->date . ' ' . $availability->end_time);

            while ($start < $end) {
                $slotEnd = clone $start;
                $slotEnd->modify('+30 minutes');

                if ($slotEnd > $end) {
                    break;
                }

                // Check if slot is available
                $isBooked = false;
                foreach ($appointments as $appointment) {
                    $appointmentTime = new \DateTime($appointment->appointment_date);
                    if ($start->format('H:i') === $appointmentTime->format('H:i')) {
                        $isBooked = true;
                        break;
                    }
                }

                $slots[] = [
                    'time' => $start->format('H:i'),
                    'available' => !$isBooked,
                ];

                $start->modify('+30 minutes');
            }
        }

        return response()->json($slots);
    }
}
