<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdultAccess extends Model
{
    protected $table = 'adult_access';

    protected $fillable = [
        'access_token',
        'email',
        'user_id',
        'created_by',
        'status',
        'max_uses',
        'uses_count',
        'expires_at',
        'used_at',
    ];

    protected $dates = [
        'expires_at',
        'used_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Utilisateur qui a utilisé le token
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin qui a créé le token
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Vérifie si le token est expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifie si le token peut encore être utilisé
     */
    public function canUse(): bool
    {
        return $this->status === 'pending' && $this->uses_count < $this->max_uses && ! $this->isExpired();
    }

    /**
     * Marque le token comme utilisé
     */
    public function markAsUsed(): void
    {
        $this->increment('uses_count');

        if ($this->uses_count >= $this->max_uses) {
            $this->status = 'used';
        }

        $this->used_at = now();
        $this->save();
    }
}
