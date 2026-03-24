<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'teacher_id', // Added teacher_id
        'level',
        'year',
    ];

    /**
     * The teacher who manages this class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * The school this class belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * The students enrolled in this class.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'user_id')
            ->withPivot('enrolled_at', 'is_active')
            ->withTimestamps();
    }

    /**
     * The books assigned to this class.
     */
    public function bookAssignments(): HasMany
    {
        return $this->hasMany(BookAssignment::class, 'class_id');
    }
}