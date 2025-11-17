<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'medecin_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'chief_complaint',
        'history_of_present_illness',
        'physical_examination',
        'diagnosis',
        'diagnosis_code',
        'treatment_plan',
        'notes',
        'vitals',
        'chat_history',
        'recommendations',
        'follow_up_instructions',
        'follow_up_days',
        'connection_quality',
        'technical_issues',
        'technical_issues_description',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'vitals' => 'array',
        'chat_history' => 'array',
        'technical_issues' => 'boolean',
        'duration_minutes' => 'integer',
        'follow_up_days' => 'integer',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
