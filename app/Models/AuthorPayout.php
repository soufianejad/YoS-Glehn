<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorPayout extends Model
{
    // Table associée (optionnel si le nom suit la convention)
    protected $table = 'author_payouts';

    // Champs assignables en masse
    protected $fillable = [
        'author_id',
        'payout_reference',
        'amount',
        'currency',
        'payment_method',
        'payment_details',
        'period_start',
        'period_end',
        'status',
        'processed_at',
    ];

    // Champs à traiter comme des dates
    protected $dates = [
        'period_start',
        'period_end',
        'processed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Relation avec l'auteur
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Vérifie si le versement est complété
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si le versement est en attente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
