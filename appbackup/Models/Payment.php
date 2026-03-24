<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'payment_type',
        'subscription_id',
        'book_id',
        'amount',
        'currency',
        'payment_method',
        'payment_provider',
        'status',
        'payment_details',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
