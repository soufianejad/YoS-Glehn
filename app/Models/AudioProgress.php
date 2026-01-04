<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioProgress extends Model
{
    use HasFactory;

    protected $table = 'audio_progress';

    protected $fillable = [
        'user_id',
        'book_id',
        'current_position',
        'total_duration',
        'progress_percentage',
        'playback_speed',
        'last_listened_at',
        'completed_at',
    ];

    protected $casts = [
        'current_position' => 'integer',
        'total_duration' => 'integer',
        'progress_percentage' => 'decimal:2',
        'playback_speed' => 'decimal:2',
        'last_listened_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function isCompleted()
    {
        return ! is_null($this->completed_at);
    }
}
