<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consultation_id',
        'patient_id',
        'medecin_id',
        'prescription_number',
        'medications',
        'recommendations',
        'renewable',
        'renewable_times',
        'qr_code',
        'digital_signature',
        'signed_at',
        'pdf_path',
        'is_valid',
        'valid_until',
    ];

    protected $casts = [
        'medications' => 'array',
        'renewable' => 'boolean',
        'renewable_times' => 'integer',
        'signed_at' => 'datetime',
        'is_valid' => 'boolean',
        'valid_until' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            if (!$prescription->prescription_number) {
                $prescription->prescription_number = 'RX-' . strtoupper(uniqid());
            }
        });
    }
}
