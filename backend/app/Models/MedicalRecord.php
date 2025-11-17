<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'allergies',
        'chronic_diseases',
        'current_treatments',
        'vaccinations',
        'medical_history',
        'family_medical_history',
    ];

    protected $casts = [
        'allergies' => 'array',
        'chronic_diseases' => 'array',
        'current_treatments' => 'array',
        'vaccinations' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
