<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'type',
        'duration_months',
        'valid_from',
        'valid_until',
        'is_active',
        'auto_renew',
        'revoked_at',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'revoked_at' => 'datetime',
        'is_active' => 'boolean',
        'auto_renew' => 'boolean',
        'duration_months' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function accesses()
    {
        return $this->hasMany(MedicalRecordAccess::class, 'consent_id');
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->valid_from->lte(now())
            && $this->valid_until->gte(now())
            && is_null($this->revoked_at);
    }

    public function revoke(): void
    {
        $this->update([
            'is_active' => false,
            'revoked_at' => now(),
        ]);
    }
}
