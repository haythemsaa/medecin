<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'appointment_date',
        'duration',
        'type',
        'status',
        'reason',
        'symptoms',
        'attached_documents',
        'price',
        'is_urgent',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'refund_amount',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'symptoms' => 'array',
        'attached_documents' => 'array',
        'price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'is_urgent' => 'boolean',
        'cancelled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->appointment_date->gt(now());
    }

    public function getRefundPercentage(): int
    {
        $hoursUntilAppointment = now()->diffInHours($this->appointment_date, false);

        if ($hoursUntilAppointment > 24) {
            return 100; // Full refund
        } elseif ($hoursUntilAppointment >= 2) {
            return 50; // Half refund
        }

        return 0; // No refund
    }
}
