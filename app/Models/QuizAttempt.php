<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'total_questions',
        'correct_answers',
        'percentage',
        'answers',
        'started_at',
        'completed_at',
        'time_spent',
        'is_passed',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'percentage' => 'decimal:2',
        'time_spent' => 'integer',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
