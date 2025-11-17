<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export appointments to CSV
     */
    public function exportAppointments(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,confirmed,completed,cancelled,no_show',
            'format' => 'nullable|in:csv,json',
        ]);

        $user = $request->user();
        $format = $request->get('format', 'csv');

        // Build query
        $query = Appointment::with(['patient.user', 'medecin.user']);

        if ($user->role === 'patient') {
            $query->where('patient_id', $user->patient->id);
        } elseif ($user->role === 'medecin') {
            $query->where('medecin_id', $user->medecin->id);
        } elseif ($user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('date', 'desc')->get();

        if ($format === 'json') {
            return response()->json([
                'appointments' => $appointments,
                'exported_at' => now()->toIso8601String(),
                'total' => $appointments->count(),
            ]);
        }

        // Generate CSV
        $csvData = $this->generateAppointmentsCsv($appointments);

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="appointments_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export consultations history
     */
    public function exportConsultations(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'nullable|in:csv,json',
        ]);

        $user = $request->user();
        $format = $request->get('format', 'csv');

        $query = Consultation::with(['appointment.patient.user', 'appointment.medecin.user']);

        if ($user->role === 'patient') {
            $query->where('patient_id', $user->patient->id);
        } elseif ($user->role === 'medecin') {
            $query->where('medecin_id', $user->medecin->id);
        } elseif ($user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($request->filled('start_date')) {
            $query->whereHas('appointment', function ($q) use ($request) {
                $q->where('date', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('appointment', function ($q) use ($request) {
                $q->where('date', '<=', $request->end_date);
            });
        }

        $consultations = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'json') {
            return response()->json([
                'consultations' => $consultations,
                'exported_at' => now()->toIso8601String(),
                'total' => $consultations->count(),
            ]);
        }

        $csvData = $this->generateConsultationsCsv($consultations);

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="consultations_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export payments history
     */
    public function exportPayments(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,completed,failed,cancelled',
            'format' => 'nullable|in:csv,json',
        ]);

        $user = $request->user();
        $format = $request->get('format', 'csv');

        $query = Payment::with(['appointment', 'user']);

        if ($user->role === 'patient') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'medecin') {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('medecin_id', $user->medecin->id);
            });
        } elseif ($user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'json') {
            return response()->json([
                'payments' => $payments,
                'exported_at' => now()->toIso8601String(),
                'total' => $payments->count(),
                'total_amount' => $payments->where('status', 'completed')->sum('amount'),
            ]);
        }

        $csvData = $this->generatePaymentsCsv($payments);

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export reviews for medecin
     */
    public function exportReviews(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'medecin' && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $format = $request->get('format', 'csv');

        $query = Review::with(['patient.user', 'appointment']);

        if ($user->role === 'medecin') {
            $query->where('medecin_id', $user->medecin->id);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'json') {
            return response()->json([
                'reviews' => $reviews,
                'exported_at' => now()->toIso8601String(),
                'total' => $reviews->count(),
                'average_rating' => $reviews->avg('overall_rating'),
            ]);
        }

        $csvData = $this->generateReviewsCsv($reviews);

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reviews_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Generate appointments CSV
     */
    private function generateAppointmentsCsv($appointments): string
    {
        $output = fopen('php://temp', 'r+');

        // Headers
        fputcsv($output, [
            'ID',
            'Date',
            'Heure',
            'Patient',
            'Médecin',
            'Type',
            'Statut',
            'Montant',
            'Statut de paiement',
            'Motif',
        ]);

        // Data
        foreach ($appointments as $appointment) {
            fputcsv($output, [
                $appointment->id,
                $appointment->date,
                $appointment->time,
                $appointment->patient->user->first_name . ' ' . $appointment->patient->user->last_name,
                $appointment->medecin->user->first_name . ' ' . $appointment->medecin->user->last_name,
                $appointment->type,
                $appointment->status,
                $appointment->montant,
                $appointment->payment_status ?? 'unpaid',
                $appointment->motif,
            ]);
        }

        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);

        return $csvData;
    }

    /**
     * Generate consultations CSV
     */
    private function generateConsultationsCsv($consultations): string
    {
        $output = fopen('php://temp', 'r+');

        fputcsv($output, [
            'ID',
            'Date',
            'Patient',
            'Médecin',
            'Diagnostic',
            'Examens',
            'Plan de traitement',
        ]);

        foreach ($consultations as $consultation) {
            fputcsv($output, [
                $consultation->id,
                $consultation->appointment->date,
                $consultation->appointment->patient->user->first_name . ' ' . $consultation->appointment->patient->user->last_name,
                $consultation->appointment->medecin->user->first_name . ' ' . $consultation->appointment->medecin->user->last_name,
                $consultation->diagnostic,
                $consultation->examen_clinique,
                $consultation->plan_traitement,
            ]);
        }

        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);

        return $csvData;
    }

    /**
     * Generate payments CSV
     */
    private function generatePaymentsCsv($payments): string
    {
        $output = fopen('php://temp', 'r+');

        fputcsv($output, [
            'ID Transaction',
            'Date',
            'Montant',
            'Méthode',
            'Statut',
            'Payé le',
            'Référence Gateway',
        ]);

        foreach ($payments as $payment) {
            fputcsv($output, [
                $payment->transaction_id,
                $payment->created_at->format('Y-m-d H:i:s'),
                $payment->amount,
                $payment->formatted_method,
                $payment->formatted_status,
                $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '',
                $payment->gateway_reference ?? '',
            ]);
        }

        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);

        return $csvData;
    }

    /**
     * Generate reviews CSV
     */
    private function generateReviewsCsv($reviews): string
    {
        $output = fopen('php://temp', 'r+');

        fputcsv($output, [
            'Date',
            'Patient',
            'Note globale',
            'Professionnalisme',
            'Écoute',
            'Explications',
            'Ponctualité',
            'Efficacité',
            'Commentaire',
        ]);

        foreach ($reviews as $review) {
            fputcsv($output, [
                $review->created_at->format('Y-m-d'),
                $review->patient->user->first_name . ' ' . $review->patient->user->last_name,
                $review->overall_rating,
                $review->rating_professionalism,
                $review->rating_listening,
                $review->rating_explanation,
                $review->rating_punctuality,
                $review->rating_effectiveness,
                $review->comment,
            ]);
        }

        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);

        return $csvData;
    }
}
