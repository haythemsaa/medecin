<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PrescriptionController extends Controller
{
    protected $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Get user's prescriptions (patient view)
     */
    public function myPrescriptions(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'patient') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $prescriptions = Prescription::whereHas('consultation.appointment', function ($query) use ($user) {
            $query->where('patient_id', $user->patient->id);
        })
        ->with([
            'consultation.medecin.user',
            'consultation.patient.user',
            'consultation.appointment'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($prescriptions);
    }

    /**
     * Get medecin's prescriptions (doctor view)
     */
    public function medecinPrescriptions(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $prescriptions = Prescription::whereHas('consultation', function ($query) use ($user) {
            $query->where('medecin_id', $user->medecin->id);
        })
        ->with([
            'consultation.medecin.user',
            'consultation.patient.user',
            'consultation.appointment'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($prescriptions);
    }

    /**
     * Get specific prescription details
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();

        $prescription = Prescription::with([
            'consultation.medecin.user',
            'consultation.patient.user',
            'consultation.appointment'
        ])->findOrFail($id);

        // Check authorization
        $isPatient = $user->role === 'patient' &&
                     $prescription->consultation->appointment->patient_id === $user->patient->id;
        $isMedecin = $user->role === 'medecin' &&
                     $prescription->consultation->medecin_id === $user->medecin->id;
        $isAdmin = $user->role === 'admin';

        if (!$isPatient && !$isMedecin && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($prescription);
    }

    /**
     * Download prescription as PDF
     */
    public function download(Request $request, $id)
    {
        $user = Auth::user();

        $prescription = Prescription::with([
            'consultation.medecin.user',
            'consultation.patient.user',
            'consultation.appointment'
        ])->findOrFail($id);

        // Check authorization
        $isPatient = $user->role === 'patient' &&
                     $prescription->consultation->appointment->patient_id === $user->patient->id;
        $isMedecin = $user->role === 'medecin' &&
                     $prescription->consultation->medecin_id === $user->medecin->id;
        $isAdmin = $user->role === 'admin';

        if (!$isPatient && !$isMedecin && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate PDF
        $pdfPath = $this->pdfService->generatePrescription($prescription);
        $fullPath = storage_path('app/public/' . $pdfPath);

        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'PDF not found'], 404);
        }

        return Response::download(
            $fullPath,
            "ordonnance_{$prescription->prescription_number}.pdf",
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * Verify prescription using QR code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'prescription_number' => 'required|string',
            'verification_code' => 'required|string'
        ]);

        $prescription = Prescription::where('prescription_number', $request->prescription_number)
            ->first();

        if (!$prescription) {
            return response()->json([
                'valid' => false,
                'message' => 'Ordonnance non trouvée'
            ], 404);
        }

        // In a real implementation, you would verify the QR code hash
        // For now, we'll just check if the prescription exists
        $isValid = $prescription &&
                   new \DateTime($prescription->valid_until) >= new \DateTime();

        return response()->json([
            'valid' => $isValid,
            'prescription' => $isValid ? [
                'prescription_number' => $prescription->prescription_number,
                'created_at' => $prescription->created_at,
                'valid_until' => $prescription->valid_until,
                'medecin' => $prescription->consultation->medecin->user->first_name . ' ' .
                            $prescription->consultation->medecin->user->last_name,
                'patient' => $prescription->consultation->patient->user->first_name . ' ' .
                            $prescription->consultation->patient->user->last_name,
            ] : null,
            'message' => $isValid ? 'Ordonnance valide' : 'Ordonnance invalide ou expirée'
        ]);
    }
}
