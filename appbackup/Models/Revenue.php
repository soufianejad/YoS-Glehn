<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'book_id',
        'payment_id',
        'total_amount',
        'author_amount',
        'platform_amount',
        'author_percentage',
        'revenue_type',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'author_amount' => 'decimal:2',
        'platform_amount' => 'decimal:2',
        'author_percentage' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
