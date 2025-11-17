<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get overview statistics for the doctor
     */
    public function getOverview(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;
        $currentMonth = Carbon::now()->startOfMonth();

        // Total consultations
        $totalConsultations = Consultation::where('medecin_id', $medecinId)->count();

        // Active patients (patients with at least one consultation)
        $activePatients = Consultation::where('medecin_id', $medecinId)
            ->distinct('patient_id')
            ->count('patient_id');

        // Monthly revenue
        $monthlyRevenue = Appointment::where('medecin_id', $medecinId)
            ->where('status', 'completed')
            ->where('date', '>=', $currentMonth)
            ->sum('montant');

        // Satisfaction rate (average rating)
        $satisfactionRate = Review::where('medecin_id', $medecinId)
            ->avg('overall_rating') ?? 0;

        return response()->json([
            'total_consultations' => $totalConsultations,
            'active_patients' => $activePatients,
            'monthly_revenue' => round($monthlyRevenue, 2),
            'satisfaction_rate' => round($satisfactionRate, 1),
        ]);
    }

    /**
     * Get revenue data over time
     */
    public function getRevenue(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;
        $period = $request->query('period', '30d');

        // Calculate start date based on period
        $startDate = match ($period) {
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            '3m' => Carbon::now()->subMonths(3),
            '1y' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };

        // Group by day or month depending on period
        $groupBy = in_array($period, ['7d', '30d']) ? 'day' : 'month';

        if ($groupBy === 'day') {
            $data = Appointment::where('medecin_id', $medecinId)
                ->where('status', 'completed')
                ->where('date', '>=', $startDate)
                ->select(
                    DB::raw('DATE(date) as period'),
                    DB::raw('SUM(montant) as total')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => Carbon::parse($item->period)->format('d/m'),
                        'value' => (float) $item->total,
                    ];
                });
        } else {
            $data = Appointment::where('medecin_id', $medecinId)
                ->where('status', 'completed')
                ->where('date', '>=', $startDate)
                ->select(
                    DB::raw('DATE_FORMAT(date, "%Y-%m") as period'),
                    DB::raw('SUM(montant) as total')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => Carbon::createFromFormat('Y-m', $item->period)->format('M Y'),
                        'value' => (float) $item->total,
                    ];
                });
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Get consultations by status
     */
    public function getConsultationsByStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;

        $data = Appointment::where('medecin_id', $medecinId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                $labels = [
                    'pending' => 'En attente',
                    'confirmed' => 'Confirmé',
                    'completed' => 'Complété',
                    'cancelled' => 'Annulé',
                    'no_show' => 'Absent',
                ];

                return [
                    'label' => $labels[$item->status] ?? $item->status,
                    'value' => $item->count,
                ];
            });

        return response()->json(['data' => $data]);
    }

    /**
     * Get consultations by month (last 12 months)
     */
    public function getConsultationsByMonth(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $data = Appointment::where('medecin_id', $medecinId)
            ->where('date', '>=', $startDate)
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'value' => $item->count,
                ];
            });

        return response()->json(['data' => $data]);
    }

    /**
     * Get patients by age groups
     */
    public function getPatientsByAge(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;

        // Get unique patients who had appointments with this doctor
        $patientIds = Appointment::where('medecin_id', $medecinId)
            ->distinct('patient_id')
            ->pluck('patient_id');

        $patients = Patient::whereIn('id', $patientIds)
            ->whereNotNull('date_naissance')
            ->get();

        $ageGroups = [
            '0-17' => 0,
            '18-30' => 0,
            '31-45' => 0,
            '46-60' => 0,
            '61+' => 0,
        ];

        foreach ($patients as $patient) {
            $age = Carbon::parse($patient->date_naissance)->age;

            if ($age < 18) {
                $ageGroups['0-17']++;
            } elseif ($age <= 30) {
                $ageGroups['18-30']++;
            } elseif ($age <= 45) {
                $ageGroups['31-45']++;
            } elseif ($age <= 60) {
                $ageGroups['46-60']++;
            } else {
                $ageGroups['61+']++;
            }
        }

        $data = collect($ageGroups)->map(function ($count, $range) {
            return [
                'label' => $range . ' ans',
                'value' => $count,
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    /**
     * Get satisfaction metrics from reviews
     */
    public function getSatisfaction(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;

        $reviews = Review::where('medecin_id', $medecinId)->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'average_rating' => 0,
                'positive_reviews_percentage' => 0,
                'average_punctuality' => 0,
                'average_listening' => 0,
                'average_professionalism' => 0,
                'average_explanation' => 0,
                'average_effectiveness' => 0,
                'total_reviews' => 0,
            ]);
        }

        $totalReviews = $reviews->count();
        $positiveReviews = $reviews->where('overall_rating', '>=', 4)->count();

        return response()->json([
            'average_rating' => $reviews->avg('overall_rating'),
            'positive_reviews_percentage' => round(($positiveReviews / $totalReviews) * 100, 1),
            'average_punctuality' => $reviews->avg('rating_punctuality'),
            'average_listening' => $reviews->avg('rating_listening'),
            'average_professionalism' => $reviews->avg('rating_professionalism'),
            'average_explanation' => $reviews->avg('rating_explanation'),
            'average_effectiveness' => $reviews->avg('rating_effectiveness'),
            'total_reviews' => $totalReviews,
        ]);
    }

    /**
     * Get appointment trends over time
     */
    public function getAppointmentTrends(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;
        $period = $request->query('period', '30d');

        // Calculate start date based on period
        $startDate = match ($period) {
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            '3m' => Carbon::now()->subMonths(3),
            '1y' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };

        // Group by day or month depending on period
        $groupBy = in_array($period, ['7d', '30d']) ? 'day' : 'month';

        if ($groupBy === 'day') {
            $data = Appointment::where('medecin_id', $medecinId)
                ->where('date', '>=', $startDate)
                ->select(
                    DB::raw('DATE(date) as period'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => Carbon::parse($item->period)->format('d/m'),
                        'value' => $item->count,
                    ];
                });
        } else {
            $data = Appointment::where('medecin_id', $medecinId)
                ->where('date', '>=', $startDate)
                ->select(
                    DB::raw('DATE_FORMAT(date, "%Y-%m") as period'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => Carbon::createFromFormat('Y-m', $item->period)->format('M Y'),
                        'value' => $item->count,
                    ];
                });
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Get top diagnoses
     */
    public function getTopDiagnoses(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'medecin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medecinId = $user->medecin->id;

        $data = Consultation::where('medecin_id', $medecinId)
            ->whereNotNull('diagnostic')
            ->where('diagnostic', '!=', '')
            ->select('diagnostic', DB::raw('COUNT(*) as count'))
            ->groupBy('diagnostic')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->diagnostic,
                    'value' => $item->count,
                ];
            });

        return response()->json(['data' => $data]);
    }
}
