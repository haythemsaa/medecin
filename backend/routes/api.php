<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AnalyticsController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AvailabilityController;
use App\Http\Controllers\API\CalendarIntegrationController;
use App\Http\Controllers\API\ConsultationController;
use App\Http\Controllers\API\ExportController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\MedecinController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\MedicationReminderController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PrescriptionController;
use App\Http\Controllers\API\QuestionnaireController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/verify-2fa', [AuthController::class, 'verify2FA']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

// Patient routes
Route::prefix('patients')->group(function () {
    Route::post('/register', [PatientController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [PatientController::class, 'show']);
        Route::put('/profile', [PatientController::class, 'update']);
    });
});

// Medecin routes
Route::prefix('medecins')->group(function () {
    Route::post('/register', [MedecinController::class, 'register']);
    Route::get('/search', [MedecinController::class, 'search']);
    Route::get('/{id}', [MedecinController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [MedecinController::class, 'show']);
        Route::put('/profile', [MedecinController::class, 'update']);
    });
});

// Appointment routes (protected)
Route::middleware('auth:sanctum')->prefix('appointments')->group(function () {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::post('/', [AppointmentController::class, 'store']);
    Route::get('/{id}', [AppointmentController::class, 'show']);
    Route::post('/{id}/cancel', [AppointmentController::class, 'cancel']);
});

// Consultation routes (protected)
Route::middleware('auth:sanctum')->prefix('consultations')->group(function () {
    Route::get('/{id}', [ConsultationController::class, 'show']);
    Route::put('/{id}/notes', [ConsultationController::class, 'updateNotes']);
    Route::post('/{id}/prescriptions', [ConsultationController::class, 'createPrescription']);
    Route::post('/{id}/report-issue', [ConsultationController::class, 'reportIssue']);
    Route::post('/appointments/{id}/room', [ConsultationController::class, 'getRoomConfig']);
    Route::post('/appointments/{id}/end', [ConsultationController::class, 'endConsultation']);
});

// Medical Record routes (protected)
Route::middleware('auth:sanctum')->prefix('medical-records')->group(function () {
    // Patient routes
    Route::get('/my-record', [MedicalRecordController::class, 'show']);
    Route::put('/my-record', [MedicalRecordController::class, 'update']);
    Route::get('/consents', [MedicalRecordController::class, 'getConsents']);
    Route::post('/consents', [MedicalRecordController::class, 'createConsent']);
    Route::delete('/consents/{id}', [MedicalRecordController::class, 'revokeConsent']);
    Route::get('/access-history', [MedicalRecordController::class, 'getAccessHistory']);

    // Medecin routes
    Route::get('/patients/{patientId}', [MedicalRecordController::class, 'access']);
});

// Prescription routes (protected)
Route::middleware('auth:sanctum')->prefix('prescriptions')->group(function () {
    Route::get('/my-prescriptions', [PrescriptionController::class, 'myPrescriptions']);
    Route::get('/medecin-prescriptions', [PrescriptionController::class, 'medecinPrescriptions']);
    Route::get('/{id}', [PrescriptionController::class, 'show']);
    Route::get('/{id}/download', [PrescriptionController::class, 'download']);
    Route::post('/verify', [PrescriptionController::class, 'verify']);
});

// Admin routes (protected)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/medecins/pending', [AdminController::class, 'getPendingValidations']);
    Route::get('/medecins/{id}', [AdminController::class, 'getMedecinForValidation']);
    Route::post('/medecins/{id}/validate', [AdminController::class, 'validateMedecin']);
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
    Route::get('/statistics', [AdminController::class, 'getStatistics']);
});

// Messages routes (protected)
Route::middleware('auth:sanctum')->prefix('messages')->group(function () {
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations/{id}/messages', [MessageController::class, 'getMessages']);
    Route::post('/conversations/{id}/messages', [MessageController::class, 'sendMessage']);
    Route::get('/unread-count', [MessageController::class, 'getUnreadCount']);
});

// Availability routes (protected)
Route::middleware('auth:sanctum')->prefix('availabilities')->group(function () {
    Route::get('/', [AvailabilityController::class, 'index']);
    Route::post('/', [AvailabilityController::class, 'store']);
    Route::put('/{id}', [AvailabilityController::class, 'update']);
    Route::delete('/{id}', [AvailabilityController::class, 'destroy']);
    Route::post('/{id}/toggle', [AvailabilityController::class, 'toggle']);
    Route::delete('/medecins/all', [AvailabilityController::class, 'destroyAll']);
    Route::get('/medecins/{medecinId}/slots', [AvailabilityController::class, 'getAvailableSlots']);
});

// Review routes (protected)
Route::middleware('auth:sanctum')->prefix('reviews')->group(function () {
    Route::get('/medecins/{medecinId}', [ReviewController::class, 'index']);
    Route::post('/', [ReviewController::class, 'store']);
    Route::put('/{id}', [ReviewController::class, 'update']);
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
    Route::post('/{id}/report', [ReviewController::class, 'report']);
});

// Analytics routes (protected - medecin only)
Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    Route::get('/overview', [AnalyticsController::class, 'getOverview']);
    Route::get('/revenue', [AnalyticsController::class, 'getRevenue']);
    Route::get('/consultations-by-status', [AnalyticsController::class, 'getConsultationsByStatus']);
    Route::get('/consultations-by-month', [AnalyticsController::class, 'getConsultationsByMonth']);
    Route::get('/patients-by-age', [AnalyticsController::class, 'getPatientsByAge']);
    Route::get('/satisfaction', [AnalyticsController::class, 'getSatisfaction']);
    Route::get('/appointment-trends', [AnalyticsController::class, 'getAppointmentTrends']);
    Route::get('/top-diagnoses', [AnalyticsController::class, 'getTopDiagnoses']);
});

// File routes (protected)
Route::middleware('auth:sanctum')->prefix('files')->group(function () {
    Route::get('/', [FileController::class, 'index']);
    Route::post('/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::post('/upload-multiple', [FileController::class, 'uploadMultiple']);
    Route::get('/{id}', [FileController::class, 'show']);
    Route::get('/{id}/download', [FileController::class, 'download'])->name('files.download');
    Route::delete('/{id}', [FileController::class, 'destroy']);
});

// Payment routes (protected)
Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::post('/initiate', [PaymentController::class, 'initiatePayment']);
    Route::post('/callback', [PaymentController::class, 'handleCallback']);
    Route::get('/{id}', [PaymentController::class, 'show']);
    Route::post('/{id}/confirm-cash', [PaymentController::class, 'confirmCashPayment']);
    Route::post('/{id}/request-refund', [PaymentController::class, 'requestRefund']);
    Route::post('/{id}/process-refund', [PaymentController::class, 'processRefund']);
});

// Search routes (public)
Route::prefix('search')->group(function () {
    Route::get('/medecins', [SearchController::class, 'searchMedecins']);
    Route::get('/autocomplete', [SearchController::class, 'autocomplete']);
    Route::get('/filters', [SearchController::class, 'getFilters']);
});

// Export routes (protected)
Route::middleware('auth:sanctum')->prefix('export')->group(function () {
    Route::get('/appointments', [ExportController::class, 'exportAppointments']);
    Route::get('/consultations', [ExportController::class, 'exportConsultations']);
    Route::get('/payments', [ExportController::class, 'exportPayments']);
    Route::get('/reviews', [ExportController::class, 'exportReviews']);
});

// Notification routes (protected)
Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/preferences', [NotificationController::class, 'getPreferences']);
    Route::put('/preferences', [NotificationController::class, 'updatePreferences']);
    Route::post('/test', [NotificationController::class, 'testNotification']);
});

// Favorites routes (protected)
Route::middleware('auth:sanctum')->prefix('favorites')->group(function () {
    Route::get('/', [FavoriteController::class, 'index']);
    Route::post('/', [FavoriteController::class, 'store']);
    Route::put('/{id}', [FavoriteController::class, 'update']);
    Route::delete('/{id}', [FavoriteController::class, 'destroy']);
    Route::get('/check/{medecinId}', [FavoriteController::class, 'check']);
});

// Questionnaire routes (protected)
Route::middleware('auth:sanctum')->prefix('questionnaires')->group(function () {
    Route::get('/templates', [QuestionnaireController::class, 'getTemplates']);
    Route::get('/appointments/{appointmentId}', [QuestionnaireController::class, 'getForAppointment']);
    Route::post('/submit', [QuestionnaireController::class, 'submit']);
    Route::post('/{id}/mark-reviewed', [QuestionnaireController::class, 'markReviewed']);
    Route::get('/medecin/all', [QuestionnaireController::class, 'getMedecinQuestionnaires']);
});

// Medication Reminder routes (protected)
Route::middleware('auth:sanctum')->prefix('medication-reminders')->group(function () {
    Route::get('/', [MedicationReminderController::class, 'index']);
    Route::post('/', [MedicationReminderController::class, 'store']);
    Route::put('/{id}', [MedicationReminderController::class, 'update']);
    Route::delete('/{id}', [MedicationReminderController::class, 'destroy']);
    Route::post('/intake/record', [MedicationReminderController::class, 'recordIntake']);
    Route::get('/{id}/intake-history', [MedicationReminderController::class, 'getIntakeHistory']);
    Route::get('/{id}/adherence-stats', [MedicationReminderController::class, 'getAdherenceStats']);
});

// Calendar Integration routes (protected)
Route::middleware('auth:sanctum')->prefix('calendar')->group(function () {
    Route::get('/status', [CalendarIntegrationController::class, 'getStatus']);
    Route::get('/google/auth-url', [CalendarIntegrationController::class, 'getGoogleAuthUrl']);
    Route::get('/outlook/auth-url', [CalendarIntegrationController::class, 'getOutlookAuthUrl']);
    Route::post('/google/callback', [CalendarIntegrationController::class, 'handleGoogleCallback']);
    Route::post('/outlook/callback', [CalendarIntegrationController::class, 'handleOutlookCallback']);
    Route::post('/google/disconnect', [CalendarIntegrationController::class, 'disconnectGoogle']);
    Route::post('/outlook/disconnect', [CalendarIntegrationController::class, 'disconnectOutlook']);
    Route::post('/auto-sync', [CalendarIntegrationController::class, 'toggleAutoSync']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Seha Digital API',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});
