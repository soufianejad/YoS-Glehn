<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Assuming you'd want factories for testing

class Page extends Model
{
    use HasFactory; // Add HasFactory trait

    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
