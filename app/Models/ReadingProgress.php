<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $table = 'reading_progress';

    protected $fillable = [
        'user_id',
        'book_id',
        'current_page',
        'total_pages',
        'progress_percentage',
        'time_spent',
        'last_read_at',
        'completed_at',
    ];

    protected $casts = [
        'current_page' => 'integer',
        'total_pages' => 'integer',
        'progress_percentage' => 'decimal:2',
        'time_spent' => 'integer',
        'last_read_at' => 'datetime',
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
