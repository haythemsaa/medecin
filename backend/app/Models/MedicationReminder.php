<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prescription_id',
        'medication_name',
        'dosage',
        'frequency',
        'reminder_times',
        'start_date',
        'end_date',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'reminder_times' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function intakes(): HasMany
    {
        return $this->hasMany(MedicationIntake::class, 'reminder_id');
    }
}
