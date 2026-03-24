<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'teacher_id',
        'name',
        'slug',
        'description',
        'level',
        'students_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'students_count' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'user_id')
            ->withPivot('enrolled_at', 'is_active')
            ->withCasts(['enrolled_at' => 'datetime'])
            ->withTimestamps();
    }

    public function bookAssignments()
    {
        return $this->hasMany(BookAssignment::class, 'class_id');
    }

    public function getTotalReadingTimeAttribute()
    {
        return $this->students->reduce(function ($carry, $student) {
            return $carry + $student->getTotalReadingTime();
        }, 0);
    }

    public function getCompletedBooksCountAttribute()
    {
        return $this->students->reduce(function ($carry, $student) {
            return $carry + $student->getCompletedBooksCount();
        }, 0);
    }

    public function getAverageQuizScoreAttribute()
    {
        $totalScore = 0;
        $attemptsCount = 0;

        foreach ($this->students as $student) {
            $totalScore += $student->quizAttempts->sum('score');
            $attemptsCount += $student->quizAttempts->count();
        }

        return $attemptsCount > 0 ? $totalScore / $attemptsCount : 0;
    }
}
