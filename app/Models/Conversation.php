<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * Get the messages for the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The users that belong to the conversation.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('last_read_at', 'archived_at', 'deleted_at')
            ->withTimestamps(); // Ensure created_at and updated_at are also available on pivot
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($conversation) {
            // When a conversation is updated, ensure its updated_at is touched
            // This is useful for ordering conversations by activity
        });
    }

    /**
     * Get the latest message of the conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
