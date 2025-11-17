<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\PrescriptionRenewal;
use App\Services\NotificationService;
use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionRenewalController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private PdfService $pdfService
    ) {}

    /**
     * Get renewable prescriptions for patient
     */
    public function getRenewablePrescriptions(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        if (!$patient) {
            return response()->json(['message' => 'Patient profile not found'], 404);
        }

        $prescriptions = Prescription::where('patient_id', $patient->id)
            ->where('is_renewable', true)
            ->where('renewals_used', '<', \DB::raw('renewals_allowed'))
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->with(['medecin.user', 'consultation'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'prescriptions' => $prescriptions->map(function ($p) {
                return [
                    'id' => $p->id,
                    'medicaments' => $p->medicaments,
                    'instructions' => $p->instructions,
                    'renewals_allowed' => $p->renewals_allowed,
                    'renewals_used' => $p->renewals_used,
                    'renewals_remaining' => $p->renewals_allowed - $p->renewals_used,
                    'valid_until' => $p->valid_until,
                    'renewal_conditions' => $p->renewal_conditions,
                    'medecin' => [
                        'id' => $p->medecin->id,
                        'nom' => $p->medecin->user->nom,
                        'prenom' => $p->medecin->user->prenom,
                        'specialite' => $p->medecin->specialite,
                    ],
                    'created_at' => $p->created_at,
                ];
            }),
        ]);
    }

    /**
     * Request prescription renewal
     */
    public function requestRenewal(Request $request): JsonResponse
    {
        $request->validate([
            'prescription_id' => 'required|exists:prescriptions,id',
            'patient_notes' => 'nullable|string|max:1000',
        ]);

        $patient = $request->user()->patient;
        if (!$patient) {
            return response()->json(['message' => 'Patient profile not found'], 404);
        }

        $prescription = Prescription::where('id', $request->prescription_id)
            ->where('patient_id', $patient->id)
            ->where('is_renewable', true)
            ->firstOrFail();

        // Validate renewal eligibility
        if ($prescription->renewals_used >= $prescription->renewals_allowed) {
            return response()->json([
                'message' => 'Nombre maximum de renouvellements atteint',
            ], 400);
        }

        if ($prescription->valid_until && Carbon::parse($prescription->valid_until)->isPast()) {
            return response()->json([
                'message' => 'Cette ordonnance a expiré',
            ], 400);
        }

        // Check for existing pending renewal
        $existingRenewal = PrescriptionRenewal::where('prescription_id', $prescription->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRenewal) {
            return response()->json([
                'message' => 'Une demande de renouvellement est déjà en cours',
            ], 400);
        }

        // Create renewal request
        $renewal = PrescriptionRenewal::create([
            'prescription_id' => $prescription->id,
            'patient_id' => $patient->id,
            'medecin_id' => $prescription->medecin_id,
            'status' => 'pending',
            'patient_notes' => $request->patient_notes,
            'requested_at' => now(),
        ]);

        // Notify doctor
        $this->notificationService->sendEmail(
            $prescription->medecin->user->email,
            'Demande de renouvellement d\'ordonnance',
            'emails.prescription-renewal-request',
            [
                'renewal' => $renewal->load(['patient.user', 'prescription']),
            ]
        );

        return response()->json([
            'message' => 'Demande de renouvellement envoyée',
            'renewal' => $renewal,
        ], 201);
    }

    /**
     * Get pending renewal requests for doctor
     */
    public function getPendingRenewals(Request $request): JsonResponse
    {
        $medecin = $request->user()->medecin;
        if (!$medecin) {
            return response()->json(['message' => 'Medecin profile not found'], 404);
        }

        $renewals = PrescriptionRenewal::where('medecin_id', $medecin->id)
            ->where('status', 'pending')
            ->with(['patient.user', 'prescription'])
            ->orderBy('requested_at', 'asc')
            ->get();

        return response()->json([
            'renewals' => $renewals,
        ]);
    }

    /**
     * Approve renewal request
     */
    public function approveRenewal(Request $request, int $renewalId): JsonResponse
    {
        $request->validate([
            'medecin_notes' => 'nullable|string|max:1000',
        ]);

        $medecin = $request->user()->medecin;
        if (!$medecin) {
            return response()->json(['message' => 'Medecin profile not found'], 404);
        }

        $renewal = PrescriptionRenewal::where('id', $renewalId)
            ->where('medecin_id', $medecin->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $prescription = $renewal->prescription;

        // Update renewal status
        $renewal->update([
            'status' => 'approved',
            'medecin_notes' => $request->medecin_notes,
            'processed_at' => now(),
            'processed_by' => $medecin->id,
        ]);

        // Increment renewals used
        $prescription->increment('renewals_used');

        // Create new prescription with same details
        $newPrescription = Prescription::create([
            'consultation_id' => $prescription->consultation_id,
            'patient_id' => $prescription->patient_id,
            'medecin_id' => $prescription->medecin_id,
            'medicaments' => $prescription->medicaments,
            'instructions' => $prescription->instructions,
            'is_renewable' => false, // New prescription is not renewable by default
            'renewals_allowed' => 0,
            'renewals_used' => 0,
        ]);

        // Generate PDF
        $this->pdfService->generatePrescriptionPdf($newPrescription);

        // Notify patient
        $this->notificationService->sendEmail(
            $renewal->patient->user->email,
            'Ordonnance renouvelée',
            'emails.prescription-renewal-approved',
            [
                'renewal' => $renewal,
                'prescription' => $newPrescription,
            ]
        );

        return response()->json([
            'message' => 'Renouvellement approuvé',
            'renewal' => $renewal,
            'new_prescription' => $newPrescription,
        ]);
    }

    /**
     * Reject renewal request
     */
    public function rejectRenewal(Request $request, int $renewalId): JsonResponse
    {
        $request->validate([
            'medecin_notes' => 'required|string|max:1000',
        ]);

        $medecin = $request->user()->medecin;
        if (!$medecin) {
            return response()->json(['message' => 'Medecin profile not found'], 404);
        }

        $renewal = PrescriptionRenewal::where('id', $renewalId)
            ->where('medecin_id', $medecin->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $renewal->update([
            'status' => 'rejected',
            'medecin_notes' => $request->medecin_notes,
            'processed_at' => now(),
            'processed_by' => $medecin->id,
        ]);

        // Notify patient
        $this->notificationService->sendEmail(
            $renewal->patient->user->email,
            'Demande de renouvellement refusée',
            'emails.prescription-renewal-rejected',
            [
                'renewal' => $renewal,
            ]
        );

        return response()->json([
            'message' => 'Renouvellement refusé',
            'renewal' => $renewal,
        ]);
    }

    /**
     * Get renewal history for patient
     */
    public function getRenewalHistory(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        if (!$patient) {
            return response()->json(['message' => 'Patient profile not found'], 404);
        }

        $renewals = PrescriptionRenewal::where('patient_id', $patient->id)
            ->with(['prescription', 'medecin.user', 'processor.user'])
            ->orderBy('requested_at', 'desc')
            ->get();

        return response()->json([
            'renewals' => $renewals,
        ]);
    }
}
