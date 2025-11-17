<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'transaction_id',
        'payment_method',
        'amount',
        'medecin_amount',
        'platform_commission',
        'vat_amount',
        'status',
        'payment_details',
        'paid_at',
        'refunded_at',
        'refund_amount',
        'refund_reason',
        'invoice_number',
        'invoice_pdf_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'medecin_amount' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    const PLATFORM_COMMISSION_RATE = 0.15; // 15%
    const VAT_RATE = 0.19; // 19%

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function calculateCommissions(float $amount): void
    {
        $this->amount = $amount;
        $this->platform_commission = $amount * self::PLATFORM_COMMISSION_RATE;
        $this->medecin_amount = $amount - $this->platform_commission;
        $this->vat_amount = $amount * self::VAT_RATE;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->transaction_id) {
                $payment->transaction_id = 'TXN-' . strtoupper(uniqid());
            }
            if (!$payment->invoice_number) {
                $payment->invoice_number = 'INV-' . date('Y') . '-' . str_pad($payment->id ?? 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
