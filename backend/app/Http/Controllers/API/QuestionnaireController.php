<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\PreConsultationQuestionnaire;
use App\Models\QuestionnaireTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    /**
     * Get questionnaire templates by specialty
     */
    public function getTemplates(Request $request): JsonResponse
    {
        $request->validate([
            'specialty' => 'nullable|string',
        ]);

        $query = QuestionnaireTemplate::query();

        if ($request->filled('specialty')) {
            $query->where('specialty', $request->specialty);
        }

        $templates = $query->where('is_active', true)
            ->orderBy('specialty')
            ->get();

        return response()->json([
            'templates' => $templates,
        ]);
    }

    /**
     * Get questionnaire for appointment
     */
    public function getForAppointment(Request $request, int $appointmentId): JsonResponse
    {
        $user = $request->user();
        $appointment = Appointment::with(['medecin'])->findOrFail($appointmentId);

        // Verify authorization
        if ($appointment->patient->user_id !== $user->id && $appointment->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $questionnaire = PreConsultationQuestionnaire::where('appointment_id', $appointmentId)->first();

        if (!$questionnaire) {
            // Get template for this specialty
            $template = QuestionnaireTemplate::where('specialty', $appointment->medecin->specialite)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                $template = QuestionnaireTemplate::where('specialty', 'Médecine générale')
                    ->where('is_active', true)
                    ->first();
            }

            return response()->json([
                'questionnaire' => null,
                'template' => $template,
                'appointment' => $appointment,
            ]);
        }

        return response()->json([
            'questionnaire' => $questionnaire,
            'appointment' => $appointment,
        ]);
    }

    /**
     * Submit pre-consultation questionnaire
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'chief_complaint' => 'required|string|max:500',
            'symptom_duration' => 'nullable|string|max:100',
            'symptom_intensity' => 'nullable|integer|min:1|max:10',
            'current_medications' => 'nullable|array',
            'allergies' => 'nullable|array',
            'previous_conditions' => 'nullable|array',
            'family_history' => 'nullable|string|max:1000',
            'lifestyle' => 'nullable|array',
            'additional_notes' => 'nullable|string|max:1000',
            'answers' => 'nullable|array',
        ]);

        $user = $request->user();
        $appointment = Appointment::findOrFail($request->appointment_id);

        // Verify this is the patient
        if ($appointment->patient->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Check if already submitted
        $existing = PreConsultationQuestionnaire::where('appointment_id', $appointment->id)->first();

        if ($existing) {
            // Update existing
            $existing->update([
                'chief_complaint' => $request->chief_complaint,
                'symptom_duration' => $request->symptom_duration,
                'symptom_intensity' => $request->symptom_intensity,
                'current_medications' => $request->current_medications,
                'allergies' => $request->allergies,
                'previous_conditions' => $request->previous_conditions,
                'family_history' => $request->family_history,
                'lifestyle' => $request->lifestyle,
                'additional_notes' => $request->additional_notes,
                'answers' => $request->answers,
                'submitted_at' => now(),
            ]);

            return response()->json([
                'message' => 'Questionnaire mis à jour avec succès',
                'questionnaire' => $existing,
            ]);
        }

        // Create new
        $questionnaire = PreConsultationQuestionnaire::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'medecin_id' => $appointment->medecin_id,
            'chief_complaint' => $request->chief_complaint,
            'symptom_duration' => $request->symptom_duration,
            'symptom_intensity' => $request->symptom_intensity,
            'current_medications' => $request->current_medications,
            'allergies' => $request->allergies,
            'previous_conditions' => $request->previous_conditions,
            'family_history' => $request->family_history,
            'lifestyle' => $request->lifestyle,
            'additional_notes' => $request->additional_notes,
            'answers' => $request->answers,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Questionnaire soumis avec succès',
            'questionnaire' => $questionnaire,
        ], 201);
    }

    /**
     * Mark questionnaire as reviewed by doctor
     */
    public function markReviewed(Request $request, int $questionnaireId): JsonResponse
    {
        $user = $request->user();
        $questionnaire = PreConsultationQuestionnaire::with('appointment')->findOrFail($questionnaireId);

        // Verify this is the medecin
        if ($questionnaire->medecin_id !== $user->medecin->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $questionnaire->update([
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Questionnaire marqué comme lu',
            'questionnaire' => $questionnaire,
        ]);
    }

    /**
     * Get questionnaires for medecin
     */
    public function getMedecinQuestionnaires(Request $request): JsonResponse
    {
        $user = $request->user();

        $questionnaires = PreConsultationQuestionnaire::where('medecin_id', $user->medecin->id)
            ->with(['appointment.patient.user'])
            ->orderBy('submitted_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'questionnaires' => $questionnaires,
        ]);
    }
}
