<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'access_code',
        'qr_code_path',
        'subscription_id',
        'max_students',
        'current_students',
        'logo',
        'banner_image',
        'primary_color',
        'is_active',
        'status',
    ];

    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image ? asset('storage/'.$this->banner_image) : asset('assets/images/school-default-banner.png');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'max_students' => 'integer',
        'current_students' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    public function bookAssignments()
    {
        return $this->hasMany(BookAssignment::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/'.$this->logo) : asset('assets/images/school-default.png');
    }

    public function hasReachedStudentLimit()
    {
        return $this->max_students && $this->current_students >= $this->max_students;
    }

    public function getActiveQuizzesCount()
    {
        return Quiz::whereHas('book.bookAssignments', function ($q) {
            $q->where('school_id', $this->id);
        })->where('is_active', true)->count();
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_school');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id', 'user_id');
    }

    public function teachers()
    {
        return $this->hasMany(User::class)->where('role', 'teacher');
    }

    public function parents()
    {
        return $this->hasMany(User::class)->where('role', 'parent');
    }
}
