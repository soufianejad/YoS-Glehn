<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'price',
        'duration_days',
        'max_students',
        'pdf_access',
        'audio_access',
        'download_access',
        'quiz_access',
        'is_active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'max_students' => 'integer',
        'pdf_access' => 'boolean',
        'audio_access' => 'boolean',
        'download_access' => 'boolean',
        'quiz_access' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isIndividual()
    {
        return $this->type === 'individual';
    }

    public function isSchool()
    {
        return $this->type === 'school';
    }
}
