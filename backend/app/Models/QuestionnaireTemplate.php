<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialty',
        'title',
        'description',
        'questions',
        'is_active',
        'order',
    ];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean',
    ];
}
