<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'title',
        'description',
        'questions_count',
        'pass_score',
        'time_limit',
        'is_active',
        'show_correct_answers',
        'randomize_questions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_correct_answers' => 'boolean',
        'randomize_questions' => 'boolean',
        'questions_count' => 'integer',
        'pass_score' => 'integer',
        'time_limit' => 'integer',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
