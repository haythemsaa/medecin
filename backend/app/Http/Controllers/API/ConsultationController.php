<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Services\PdfService;
use App\Services\WebRTCService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    protected WebRTCService $webrtcService;
    protected PdfService $pdfService;

    public function __construct(WebRTCService $webrtcService, PdfService $pdfService)
    {
        $this->webrtcService = $webrtcService;
        $this->pdfService = $pdfService;
    }

    /**
     * Get WebRTC room configuration for consultation
     */
    public function getRoomConfig(Request $request, $appointmentId)
    {
        $appointment = Appointment::with(['patient', 'medecin'])->findOrFail($appointmentId);

        // Verify user is participant
        $user = $request->user();
        if ($appointment->patient->user_id !== $user->id && $appointment->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Check if appointment is ready to join
        $appointmentTime = $appointment->appointment_date;
        $now = now();
        $diffMinutes = $appointmentTime->diffInMinutes($now, false);

        if ($diffMinutes > 10) {
            return response()->json([
                'message' => 'Vous pourrez rejoindre la consultation 10 minutes avant l\'heure prévue'
            ], 422);
        }

        // Create WebRTC room
        $roomConfig = $this->webrtcService->createRoom($appointment);

        // Start consultation if medecin joins
        if ($user->id === $appointment->medecin->user_id) {
            $this->webrtcService->startConsultation($appointment, $user->id);
        }

        return response()->json($roomConfig);
    }

    /**
     * Get consultation details
     */
    public function show(Request $request, $id)
    {
        $consultation = Consultation::with([
            'appointment',
            'patient',
            'medecin',
            'prescriptions'
        ])->findOrFail($id);

        // Verify access
        $user = $request->user();
        if ($consultation->patient->user_id !== $user->id && $consultation->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($consultation);
    }

    /**
     * Update consultation notes (medecin only)
     */
    public function updateNotes(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        // Verify medecin
        $user = $request->user();
        if ($consultation->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'chief_complaint' => 'nullable|string',
            'history_of_present_illness' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'diagnosis_code' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'follow_up_instructions' => 'nullable|string',
            'follow_up_days' => 'nullable|integer',
            'vitals' => 'nullable|array'
        ]);

        $consultation->update($request->only([
            'chief_complaint',
            'history_of_present_illness',
            'physical_examination',
            'diagnosis',
            'diagnosis_code',
            'treatment_plan',
            'notes',
            'recommendations',
            'follow_up_instructions',
            'follow_up_days',
            'vitals'
        ]));

        return response()->json([
            'message' => 'Notes de consultation mises à jour',
            'consultation' => $consultation
        ]);
    }

    /**
     * Create prescription
     */
    public function createPrescription(Request $request, $consultationId)
    {
        $consultation = Consultation::findOrFail($consultationId);

        // Verify medecin
        $user = $request->user();
        if ($consultation->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'medications' => 'required|array',
            'medications.*.name' => 'required|string',
            'medications.*.dosage' => 'required|string',
            'medications.*.frequency' => 'required|string',
            'medications.*.duration' => 'required|string',
            'recommendations' => 'nullable|string',
            'renewable' => 'boolean',
            'renewable_times' => 'nullable|integer'
        ]);

        try {
            DB::beginTransaction();

            $prescription = Prescription::create([
                'consultation_id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'medecin_id' => $consultation->medecin_id,
                'medications' => $request->medications,
                'recommendations' => $request->recommendations,
                'renewable' => $request->renewable ?? false,
                'renewable_times' => $request->renewable_times,
                'signed_at' => now(),
                'is_valid' => true,
                'valid_until' => now()->addMonths(3)
            ]);

            // Generate PDF
            $this->pdfService->generatePrescription($prescription);

            DB::commit();

            return response()->json([
                'message' => 'Ordonnance créée avec succès',
                'prescription' => $prescription->fresh()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la création de l\'ordonnance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * End consultation
     */
    public function endConsultation(Request $request, $appointmentId)
    {
        $appointment = Appointment::with('consultation')->findOrFail($appointmentId);

        // Verify medecin
        $user = $request->user();
        if ($appointment->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $this->webrtcService->endConsultation($appointment);

        return response()->json([
            'message' => 'Consultation terminée',
            'appointment' => $appointment->fresh()
        ]);
    }

    /**
     * Report technical issue
     */
    public function reportIssue(Request $request, $consultationId)
    {
        $consultation = Consultation::findOrFail($consultationId);

        $request->validate([
            'description' => 'required|string'
        ]);

        $this->webrtcService->reportTechnicalIssue($consultation, $request->description);

        return response()->json(['message' => 'Problème signalé']);
    }
}
