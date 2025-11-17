<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationIntake extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_id',
        'user_id',
        'taken_at',
        'skipped',
        'notes',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'skipped' => 'boolean',
    ];

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(MedicationReminder::class, 'reminder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
