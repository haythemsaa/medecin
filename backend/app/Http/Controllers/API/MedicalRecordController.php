<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalConsent;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAccess;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Get patient's medical record
     */
    public function show(Request $request)
    {
        $patient = $request->user()->patient;
        $medicalRecord = $patient->medicalRecord;

        if (!$medicalRecord) {
            $medicalRecord = MedicalRecord::create(['patient_id' => $patient->id]);
        }

        return response()->json($medicalRecord);
    }

    /**
     * Update medical record
     */
    public function update(Request $request)
    {
        $patient = $request->user()->patient;
        $medicalRecord = $patient->medicalRecord;

        $request->validate([
            'allergies' => 'nullable|array',
            'chronic_diseases' => 'nullable|array',
            'current_treatments' => 'nullable|array',
            'vaccinations' => 'nullable|array',
            'medical_history' => 'nullable|string',
            'family_medical_history' => 'nullable|string'
        ]);

        $medicalRecord->update($request->all());

        return response()->json([
            'message' => 'Dossier médical mis à jour',
            'medical_record' => $medicalRecord
        ]);
    }

    /**
     * Get medical consents
     */
    public function getConsents(Request $request)
    {
        $patient = $request->user()->patient;

        $consents = MedicalConsent::where('patient_id', $patient->id)
            ->with('medecin.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($consents);
    }

    /**
     * Create medical consent
     */
    public function createConsent(Request $request)
    {
        $patient = $request->user()->patient;

        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'type' => 'required|in:single,extended,emergency',
            'duration_months' => 'nullable|integer|min:1|max:12'
        ]);

        $validFrom = now();
        $validUntil = $request->type === 'single'
            ? now()->addDays(1)
            : now()->addMonths($request->duration_months ?? 6);

        $consent = MedicalConsent::create([
            'patient_id' => $patient->id,
            'medecin_id' => $request->medecin_id,
            'type' => $request->type,
            'duration_months' => $request->duration_months,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'is_active' => true,
            'auto_renew' => $request->auto_renew ?? false
        ]);

        return response()->json([
            'message' => 'Consentement créé',
            'consent' => $consent
        ], 201);
    }

    /**
     * Revoke consent
     */
    public function revokeConsent(Request $request, $id)
    {
        $consent = MedicalConsent::findOrFail($id);

        // Verify ownership
        $patient = $request->user()->patient;
        if ($consent->patient_id !== $patient->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $consent->revoke();

        return response()->json(['message' => 'Consentement révoqué']);
    }

    /**
     * Get access history
     */
    public function getAccessHistory(Request $request)
    {
        $patient = $request->user()->patient;

        $accesses = MedicalRecordAccess::where('patient_id', $patient->id)
            ->with('medecin.user')
            ->orderBy('accessed_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($accesses);
    }

    /**
     * Access medical record (medecin)
     */
    public function access(Request $request, $patientId)
    {
        $medecin = $request->user()->medecin;

        // Check consent
        $consent = MedicalConsent::where('patient_id', $patientId)
            ->where('medecin_id', $medecin->id)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->whereNull('revoked_at')
            ->first();

        if (!$consent) {
            return response()->json([
                'message' => 'Vous n\'avez pas l\'autorisation d\'accéder à ce dossier médical'
            ], 403);
        }

        // Log access
        MedicalRecordAccess::create([
            'patient_id' => $patientId,
            'medecin_id' => $medecin->id,
            'consent_id' => $consent->id,
            'accessed_section' => 'full_record',
            'ip_address' => $request->ip(),
            'accessed_at' => now()
        ]);

        // Get medical record
        $medicalRecord = MedicalRecord::where('patient_id', $patientId)->firstOrFail();

        return response()->json($medicalRecord);
    }
}
