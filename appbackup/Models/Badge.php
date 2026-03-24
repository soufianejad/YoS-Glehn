<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'books_required',
        'minutes_required',
        'quizzes_required',
        'points',
        'is_active',
    ];

    protected $casts = [
        'books_required' => 'integer',
        'minutes_required' => 'integer',
        'quizzes_required' => 'integer',
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->using(\App\Models\UserBadge::class) // 👈 important !
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    /**
     * Get the full URL for the badge's icon.
     */
    public function getIconUrlAttribute(): string
    {
        return $this->icon
            ? asset('storage/'.$this->icon)
            : asset('images/default_badge_icon.png'); // Ensure you have a default icon at this path
    }
}
