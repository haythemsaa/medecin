<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalConsent;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Create a new appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'appointment_date' => 'required|date|after:now',
            'type' => 'required|in:video,phone',
            'reason' => 'required|string',
            'symptoms' => 'nullable|array',
            'is_urgent' => 'boolean',
        ]);

        $patient = $request->user()->patient;
        $medecin = \App\Models\Medecin::findOrFail($request->medecin_id);

        // Check if appointment time is available
        $existingAppointment = Appointment::where('medecin_id', $medecin->id)
            ->where('appointment_date', $request->appointment_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingAppointment) {
            return response()->json([
                'message' => 'Ce créneau n\'est plus disponible.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Calculate price
            $price = $request->is_urgent
                ? $medecin->urgent_consultation_price
                : $medecin->consultation_price;

            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'appointment_date' => $request->appointment_date,
                'duration' => 30, // Default duration
                'type' => $request->type,
                'status' => 'pending',
                'reason' => $request->reason,
                'symptoms' => $request->symptoms,
                'price' => $price,
                'is_urgent' => $request->is_urgent ?? false,
            ]);

            // Create medical consent (single use for this consultation)
            MedicalConsent::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'type' => 'single',
                'valid_from' => now(),
                'valid_until' => $request->appointment_date,
                'is_active' => true,
            ]);

            // Create payment record
            $payment = new Payment();
            $payment->patient_id = $patient->id;
            $payment->appointment_id = $appointment->id;
            $payment->payment_method = $request->payment_method ?? 'card';
            $payment->calculateCommissions($price);
            $payment->status = 'pending';
            $payment->save();

            DB::commit();

            return response()->json([
                'message' => 'Rendez-vous créé avec succès',
                'appointment' => $appointment->load(['medecin.user', 'patient', 'payment']),
                'payment' => $payment,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la création du rendez-vous',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get appointments
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Appointment::query();

        if ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        } elseif ($user->isMedecin()) {
            $query->where('medecin_id', $user->medecin->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('appointment_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('appointment_date', '<=', $request->to_date);
        }

        $appointments = $query
            ->with(['patient', 'medecin.user', 'payment'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(20);

        return response()->json($appointments);
    }

    /**
     * Get single appointment
     */
    public function show(Request $request, $id)
    {
        $appointment = Appointment::with([
            'patient',
            'medecin.user',
            'payment',
            'consultation',
            'review'
        ])->findOrFail($id);

        // Check authorization
        $user = $request->user();
        if ($user->isPatient() && $appointment->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        if ($user->isMedecin() && $appointment->medecin_id !== $user->medecin->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($appointment);
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = $request->user();

        // Check authorization
        if ($user->isPatient() && $appointment->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        if ($user->isMedecin() && $appointment->medecin_id !== $user->medecin->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if (!$appointment->canBeCancelled()) {
            return response()->json([
                'message' => 'Ce rendez-vous ne peut plus être annulé',
            ], 422);
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Calculate refund
            $refundPercentage = $appointment->getRefundPercentage();
            $refundAmount = ($appointment->price * $refundPercentage) / 100;

            // Update appointment
            $appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason,
                'cancelled_by' => $user->role,
                'refund_amount' => $refundAmount,
            ]);

            // Process refund if applicable
            if ($refundAmount > 0 && $appointment->payment) {
                $appointment->payment->update([
                    'status' => 'refunded',
                    'refund_amount' => $refundAmount,
                    'refund_reason' => $request->reason,
                    'refunded_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Rendez-vous annulé avec succès',
                'refund_amount' => $refundAmount,
                'appointment' => $appointment,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de l\'annulation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
