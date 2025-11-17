<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
        'appointment_reminders',
        'appointment_confirmations',
        'appointment_cancellations',
        'new_messages',
        'prescription_ready',
        'review_reminders',
        'marketing_emails',
        'reminder_hours_before',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'appointment_reminders' => 'boolean',
        'appointment_confirmations' => 'boolean',
        'appointment_cancellations' => 'boolean',
        'new_messages' => 'boolean',
        'prescription_ready' => 'boolean',
        'review_reminders' => 'boolean',
        'marketing_emails' => 'boolean',
        'reminder_hours_before' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
