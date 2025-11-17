<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medecin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'cin',
        'cin_file_recto',
        'cin_file_verso',
        'ordre_number',
        'ordre_certificate',
        'diploma_file',
        'rcp_attestation',
        'rcp_expiry_date',
        'rib',
        'speciality',
        'sub_specialities',
        'years_of_experience',
        'bio',
        'photo',
        'consultation_languages',
        'consultation_price',
        'urgent_consultation_price',
        'validation_status',
        'validation_notes',
        'validated_at',
        'validated_by',
        'rating_average',
        'rating_count',
        'consultation_count',
    ];

    protected $casts = [
        'sub_specialities' => 'array',
        'consultation_languages' => 'array',
        'rcp_expiry_date' => 'date',
        'validated_at' => 'datetime',
        'consultation_price' => 'decimal:2',
        'urgent_consultation_price' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'years_of_experience' => 'integer',
        'rating_count' => 'integer',
        'consultation_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function medicalConsents()
    {
        return $this->hasMany(MedicalConsent::class);
    }

    public function getFullNameAttribute(): string
    {
        return "Dr. {$this->first_name} {$this->last_name}";
    }

    public function isValidated(): bool
    {
        return $this->validation_status === 'validated';
    }

    public function updateRating(): void
    {
        $this->rating_average = $this->reviews()->avg('overall_rating');
        $this->rating_count = $this->reviews()->count();
        $this->save();
    }
}
