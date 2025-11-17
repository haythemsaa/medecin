<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Medecin;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get admin dashboard stats
     */
    public function dashboard(Request $request)
    {
        // Verify admin
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $stats = [
            'users' => [
                'total' => User::count(),
                'patients' => User::where('role', 'patient')->count(),
                'medecins' => User::where('role', 'medecin')->count(),
                'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            ],
            'medecins' => [
                'pending_validation' => Medecin::where('validation_status', 'pending')->count(),
                'validated' => Medecin::where('validation_status', 'validated')->count(),
                'rejected' => Medecin::where('validation_status', 'rejected')->count(),
            ],
            'appointments' => [
                'today' => Appointment::whereDate('appointment_date', today())->count(),
                'this_month' => Appointment::whereMonth('appointment_date', now()->month)->count(),
                'completed' => Appointment::where('status', 'completed')->count(),
                'cancelled' => Appointment::where('status', 'cancelled')->count(),
            ],
            'revenue' => [
                'today' => Payment::whereDate('created_at', today())->where('status', 'completed')->sum('platform_commission'),
                'this_month' => Payment::whereMonth('created_at', now()->month)->where('status', 'completed')->sum('platform_commission'),
                'total' => Payment::where('status', 'completed')->sum('platform_commission'),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Get pending medecin validations
     */
    public function getPendingValidations(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $medecins = Medecin::where('validation_status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return response()->json($medecins);
    }

    /**
     * Get medecin details for validation
     */
    public function getMedecinForValidation(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $medecin = Medecin::with('user')->findOrFail($id);

        return response()->json($medecin);
    }

    /**
     * Validate or reject medecin
     */
    public function validateMedecin(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'status' => 'required|in:validated,rejected,incomplete',
            'notes' => 'nullable|string'
        ]);

        $medecin = Medecin::with('user')->findOrFail($id);

        try {
            DB::beginTransaction();

            $medecin->update([
                'validation_status' => $request->status,
                'validation_notes' => $request->notes,
                'validated_at' => $request->status === 'validated' ? now() : null,
                'validated_by' => $request->user()->id,
            ]);

            // Update user status
            if ($request->status === 'validated') {
                $medecin->user->update(['status' => 'active']);
            }

            // Send notification
            $this->notificationService->sendMedecinValidation(
                $medecin->user,
                $request->status === 'validated'
            );

            DB::commit();

            return response()->json([
                'message' => $request->status === 'validated'
                    ? 'Médecin validé avec succès'
                    : 'Demande traitée',
                'medecin' => $medecin
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la validation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users (with filters)
     */
    public function getUsers(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $query = User::query()->with(['patient', 'medecin']);

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by email or phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($users);
    }

    /**
     * Suspend or activate user
     */
    public function updateUserStatus(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $user = User::findOrFail($id);
        $user->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Statut utilisateur mis à jour',
            'user' => $user
        ]);
    }

    /**
     * Get system statistics
     */
    public function getStatistics(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $period = $request->get('period', '30days'); // 7days, 30days, 90days, year

        $stats = [
            'appointments_trend' => $this->getAppointmentsTrend($period),
            'revenue_trend' => $this->getRevenueTrend($period),
            'user_growth' => $this->getUserGrowth($period),
            'top_specialities' => $this->getTopSpecialities(),
            'top_medecins' => $this->getTopMedecins(),
        ];

        return response()->json($stats);
    }

    private function getAppointmentsTrend($period)
    {
        $days = match($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            'year' => 365,
            default => 30
        };

        return Appointment::selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
            ->where('appointment_date', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRevenueTrend($period)
    {
        $days = match($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            'year' => 365,
            default => 30
        };

        return Payment::selectRaw('DATE(created_at) as date, SUM(platform_commission) as revenue')
            ->where('created_at', '>=', now()->subDays($days))
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getUserGrowth($period)
    {
        $days = match($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            'year' => 365,
            default => 30
        };

        return User::selectRaw('DATE(created_at) as date, role, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date', 'role')
            ->orderBy('date')
            ->get();
    }

    private function getTopSpecialities()
    {
        return Medecin::selectRaw('speciality, COUNT(*) as count')
            ->where('validation_status', 'validated')
            ->groupBy('speciality')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    private function getTopMedecins()
    {
        return Medecin::where('validation_status', 'validated')
            ->orderByDesc('rating_average')
            ->orderByDesc('consultation_count')
            ->with('user')
            ->limit(10)
            ->get();
    }
}
