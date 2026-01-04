<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * MODEL: Category
 * ========================================
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'space',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('space', 'public');
    }

    public function scopeEducational($query)
    {
        return $query->where('space', 'educational');
    }

    public function scopeAdult($query)
    {
        return $query->where('space', 'adult');
    }
}
