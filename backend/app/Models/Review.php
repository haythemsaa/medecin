<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'medecin_id',
        'quality_rating',
        'listening_rating',
        'clarity_rating',
        'punctuality_rating',
        'value_rating',
        'overall_rating',
        'comment',
        'would_recommend',
        'status',
        'medecin_response',
        'responded_at',
        'is_verified',
    ];

    protected $casts = [
        'quality_rating' => 'decimal:1',
        'listening_rating' => 'decimal:1',
        'clarity_rating' => 'decimal:1',
        'punctuality_rating' => 'decimal:1',
        'value_rating' => 'decimal:1',
        'overall_rating' => 'decimal:1',
        'would_recommend' => 'boolean',
        'is_verified' => 'boolean',
        'responded_at' => 'datetime',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            $review->overall_rating = (
                $review->quality_rating +
                $review->listening_rating +
                $review->clarity_rating +
                $review->punctuality_rating +
                $review->value_rating
            ) / 5;
        });

        static::saved(function ($review) {
            $review->medecin->updateRating();
        });
    }
}
