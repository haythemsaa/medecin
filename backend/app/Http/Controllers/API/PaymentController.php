<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initiate payment for an appointment
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'payment_method' => 'required|in:card,cash,e-dinar,mobile_money',
        ]);

        $user = $request->user();
        $appointment = Appointment::with(['patient', 'medecin'])->findOrFail($request->appointment_id);

        // Verify user is the patient for this appointment
        if ($appointment->patient->user_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorisé à effectuer ce paiement',
            ], 403);
        }

        // Check if payment already exists
        $existingPayment = Payment::where('appointment_id', $appointment->id)
            ->whereIn('status', ['completed', 'pending'])
            ->first();

        if ($existingPayment) {
            return response()->json([
                'message' => 'Un paiement existe déjà pour ce rendez-vous',
                'payment' => $existingPayment,
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Create payment record
            $payment = Payment::create([
                'appointment_id' => $appointment->id,
                'user_id' => $user->id,
                'amount' => $appointment->montant,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cash' ? 'pending' : 'pending',
                'transaction_id' => 'TXN-' . strtoupper(Str::random(16)),
                'payment_metadata' => [
                    'medecin_name' => $appointment->medecin->user->first_name . ' ' . $appointment->medecin->user->last_name,
                    'appointment_date' => $appointment->date,
                    'appointment_time' => $appointment->time,
                ],
            ]);

            // For cash payments, mark as pending confirmation
            if ($request->payment_method === 'cash') {
                DB::commit();

                return response()->json([
                    'message' => 'Paiement en espèces enregistré. Confirmez le paiement lors du rendez-vous.',
                    'payment' => $payment,
                ], 201);
            }

            // For online payments, generate payment gateway URL
            $paymentGatewayResponse = $this->processOnlinePayment($payment, $request->payment_method);

            DB::commit();

            return response()->json([
                'message' => 'Paiement initié avec succès',
                'payment' => $payment,
                'payment_url' => $paymentGatewayResponse['url'] ?? null,
                'payment_token' => $paymentGatewayResponse['token'] ?? null,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de l\'initiation du paiement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process online payment (integration point for payment gateways)
     */
    private function processOnlinePayment(Payment $payment, string $method): array
    {
        // This is where you would integrate with actual payment gateways
        // For Tunisia: e-Dinar, Tunisie Telecom mobile money, etc.

        // Example integration points:
        switch ($method) {
            case 'card':
                return $this->processCardPayment($payment);
            case 'e-dinar':
                return $this->processEDinarPayment($payment);
            case 'mobile_money':
                return $this->processMobileMoneyPayment($payment);
            default:
                return ['url' => null, 'token' => null];
        }
    }

    /**
     * Process card payment (integration with Tunisian banks)
     */
    private function processCardPayment(Payment $payment): array
    {
        // TODO: Integrate with Tunisian payment gateway (SMT, ATB, etc.)
        // For now, return mock data

        return [
            'url' => config('app.url') . "/payment/gateway/{$payment->transaction_id}",
            'token' => base64_encode(json_encode([
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'timestamp' => now()->timestamp,
            ])),
        ];
    }

    /**
     * Process e-Dinar payment
     */
    private function processEDinarPayment(Payment $payment): array
    {
        // TODO: Integrate with e-Dinar Tunisie API

        return [
            'url' => "https://edinar-payment-gateway.tn/pay/{$payment->transaction_id}",
            'token' => Str::random(32),
        ];
    }

    /**
     * Process mobile money payment
     */
    private function processMobileMoneyPayment(Payment $payment): array
    {
        // TODO: Integrate with Tunisie Telecom mobile money or Orange Money

        return [
            'url' => "https://mobile-money.tn/pay/{$payment->transaction_id}",
            'token' => Str::random(32),
        ];
    }

    /**
     * Handle payment callback from gateway
     */
    public function handleCallback(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|in:completed,failed,cancelled',
            'gateway_reference' => 'nullable|string',
        ]);

        $payment = Payment::where('transaction_id', $request->transaction_id)->first();

        if (!$payment) {
            return response()->json([
                'message' => 'Paiement introuvable',
            ], 404);
        }

        // Update payment status
        $payment->update([
            'status' => $request->status,
            'gateway_reference' => $request->gateway_reference,
            'paid_at' => $request->status === 'completed' ? now() : null,
            'payment_metadata' => array_merge($payment->payment_metadata ?? [], [
                'callback_data' => $request->all(),
                'callback_received_at' => now()->toIso8601String(),
            ]),
        ]);

        // Update appointment status if payment completed
        if ($request->status === 'completed') {
            $payment->appointment->update([
                'payment_status' => 'paid',
                'status' => $payment->appointment->status === 'pending' ? 'confirmed' : $payment->appointment->status,
            ]);
        }

        return response()->json([
            'message' => 'Statut de paiement mis à jour',
            'payment' => $payment,
        ]);
    }

    /**
     * Confirm cash payment (medecin only)
     */
    public function confirmCashPayment(Request $request, int $paymentId): JsonResponse
    {
        $user = $request->user();
        $payment = Payment::with('appointment.medecin')->findOrFail($paymentId);

        // Verify user is the medecin for this appointment
        if ($payment->appointment->medecin->user_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorisé à confirmer ce paiement',
            ], 403);
        }

        if ($payment->payment_method !== 'cash') {
            return response()->json([
                'message' => 'Seuls les paiements en espèces peuvent être confirmés manuellement',
            ], 422);
        }

        if ($payment->status === 'completed') {
            return response()->json([
                'message' => 'Ce paiement est déjà confirmé',
            ], 422);
        }

        // Update payment
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'payment_metadata' => array_merge($payment->payment_metadata ?? [], [
                'confirmed_by' => $user->id,
                'confirmed_at' => now()->toIso8601String(),
            ]),
        ]);

        // Update appointment
        $payment->appointment->update([
            'payment_status' => 'paid',
            'status' => $payment->appointment->status === 'pending' ? 'confirmed' : $payment->appointment->status,
        ]);

        return response()->json([
            'message' => 'Paiement confirmé avec succès',
            'payment' => $payment,
        ]);
    }

    /**
     * Get payment details
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $payment = Payment::with(['appointment.patient', 'appointment.medecin'])->findOrFail($id);

        // Check authorization
        $isPatient = $payment->user_id === $user->id;
        $isMedecin = $payment->appointment->medecin->user_id === $user->id;
        $isAdmin = $user->role === 'admin';

        if (!$isPatient && !$isMedecin && !$isAdmin) {
            return response()->json([
                'message' => 'Non autorisé à accéder à ce paiement',
            ], 403);
        }

        return response()->json([
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'paid_at' => $payment->paid_at,
                'created_at' => $payment->created_at,
                'appointment' => [
                    'id' => $payment->appointment->id,
                    'date' => $payment->appointment->date,
                    'time' => $payment->appointment->time,
                ],
            ],
        ]);
    }

    /**
     * Get user's payment history
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Payment::with('appointment');

        if ($user->role === 'patient') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'medecin') {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('medecin_id', $user->medecin->id);
            });
        } elseif ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'payments' => $payments->map(fn($payment) => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'paid_at' => $payment->paid_at,
                'created_at' => $payment->created_at,
                'appointment_id' => $payment->appointment_id,
            ]),
        ]);
    }

    /**
     * Request refund
     */
    public function requestRefund(Request $request, int $paymentId): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user = $request->user();
        $payment = Payment::with('appointment')->findOrFail($paymentId);

        // Verify user owns this payment
        if ($payment->user_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        // Check if refund is possible
        if ($payment->status !== 'completed') {
            return response()->json([
                'message' => 'Seuls les paiements complétés peuvent être remboursés',
            ], 422);
        }

        if ($payment->refund_status === 'completed') {
            return response()->json([
                'message' => 'Ce paiement a déjà été remboursé',
            ], 422);
        }

        // Update payment
        $payment->update([
            'refund_status' => 'requested',
            'refund_reason' => $request->reason,
            'refund_requested_at' => now(),
        ]);

        return response()->json([
            'message' => 'Demande de remboursement enregistrée',
            'payment' => $payment,
        ]);
    }

    /**
     * Process refund (admin only)
     */
    public function processRefund(Request $request, int $paymentId): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string',
        ]);

        $payment = Payment::findOrFail($paymentId);

        if ($payment->refund_status !== 'requested') {
            return response()->json([
                'message' => 'Aucune demande de remboursement en attente',
            ], 422);
        }

        if ($request->action === 'approve') {
            // TODO: Process actual refund with payment gateway

            $payment->update([
                'refund_status' => 'completed',
                'refund_processed_at' => now(),
                'refund_admin_notes' => $request->admin_notes,
            ]);

            $message = 'Remboursement approuvé et traité';
        } else {
            $payment->update([
                'refund_status' => 'rejected',
                'refund_admin_notes' => $request->admin_notes,
            ]);

            $message = 'Demande de remboursement rejetée';
        }

        return response()->json([
            'message' => $message,
            'payment' => $payment,
        ]);
    }
}
