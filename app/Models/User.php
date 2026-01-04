<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User
 * Gère tous les types d'utilisateurs (admin, auteur, école, élève, lecteur)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'language',
        'is_active',
        'is_verified',
        'email_verified_at',
        'school_id',
        'school_code',
        'parent_id', // Added parent_id
        'can_receive_messages',
        'notification_preferences',
    ];

    // Relations
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'can_receive_messages' => 'boolean',
        'notification_preferences' => 'array',
    ];

    // Accesseurs
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : asset('/images/default-avatar.png');
    }

    // Vérification des rôles
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    public function isSchool(): bool
    {
        return $this->role === 'school';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isReader(): bool
    {
        return $this->role === 'reader';
    }

    public function isAdultReader(): bool
    {
        return $this->role === 'adult_reader';
    }

    // Relations

    // Livres publiés (si auteur)
    public function books()
    {
        return $this->hasMany(Book::class, 'author_id');
    }

    // Classes gérées (si professeur)
    public function managedClasses()
    {
        return $this->hasMany(Classe::class, 'teacher_id');
    }

    // École associée (si élève)
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // École gérée (si compte école)
    public function managedSchool()
    {
        return $this->hasOne(School::class, 'user_id');
    }

    // Abonnements
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Abonnement actif
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('end_date', '>=', now());
    }

    // Paiements
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Achats individuels
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // Progress de lecture
    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    // Progress audio
    public function audioProgress()
    {
        return $this->hasMany(AudioProgress::class);
    }

    // Favoris
    public function favorites()
    {
        return $this->belongsToMany(Book::class, 'favorites')
            ->withTimestamps();
    }

    // Avis/Reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Tentatives de quiz
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Classes (si étudiant)
    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_student', 'user_id', 'class_id')
            ->withPivot('enrolled_at', 'is_active')
            ->withCasts(['enrolled_at' => 'datetime'])
            ->withTimestamps();
    }

    // Badges obtenus
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->using(\App\Models\UserBadge::class)
            ->withPivot('earned_at')
            ->withCasts(['earned_at' => 'datetime'])
            ->withTimestamps();
    }

    // Revenus (si auteur)
    public function revenues()
    {
        return $this->hasMany(Revenue::class, 'author_id');
    }

    // Versements (si auteur)
    public function payouts()
    {
        return $this->hasMany(AuthorPayout::class, 'author_id');
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Messages envoyés par l'utilisateur
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Marque-pages
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    // Conversations auxquelles l'utilisateur participe
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withPivot('last_read_at', 'archived_at', 'deleted_at')
            ->withTimestamps();
    }

    // Notifications non lues
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)
            ->where('is_read', false);
    }

    // Méthodes utilitaires

    // Vérifier si l'utilisateur a un abonnement actif
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    // Vérifier si l'utilisateur a accès à un livre
    public function hasAccessToBook(Book $book): bool
    {
        // Si admin, accès total
        if ($this->isAdmin()) {
            return true;
        }

        // Si auteur du livre
        if ($book->author_id === $this->id) {
            return true;
        }

        // Si l'utilisateur est un étudiant
        if ($this->isStudent()) {

            // Eager load school and subscription if not already loaded
            if (! $this->relationLoaded('school')) {
                $this->load('school.subscription');
            } elseif ($this->school && ! $this->school->relationLoaded('subscription')) {
                $this->school->load('subscription');
            }

            // Si le livre est assigné à l'une de ses classes
            if ($this->classes()->whereHas('bookAssignments', function ($query) use ($book) {
                $query->where('book_id', $book->id)
                    ->where(function ($q) {
                        $q->whereNull('due_date')
                            ->orWhere('due_date', '>=', now());
                    });
            })->exists()) {
                return true;
            }
            // Si le livre est dans l'espace éducatif et que l'école de l'étudiant a un abonnement actif
            if ($book->space === 'educational' && $this->school && $this->school->subscription && $this->school->subscription->isActive()) {
                return true;
            }
        }

        // Si abonnement actif (pour lecteurs individuels)
        if ($this->hasActiveSubscription()) {
            return true;
        }

        // Si achat individuel
        return $this->purchases()
            ->where('book_id', $book->id)
            ->where('is_active', true)
            ->exists();
    }

    // Obtenir le temps total de lecture
    public function canDownloadBook(Book $book): bool
    {
        if (! $book->is_downloadable) {
            return false;
        }

        return $this->purchases()
            ->where('book_id', $book->id)
            ->where('purchase_type', 'pdf_download')
            ->where('is_active', true)
            ->exists();
    }

    public function getReadingMinutes(): int
    {
        $readingSeconds = $this->readingProgress()->sum('time_spent');
        $audioSeconds = $this->audioProgress()->sum('current_position');
        return floor(($readingSeconds + $audioSeconds) / 60);
    }

    // Obtenir le nombre de livres lus/écoutés
    public function getCompletedBooksCount(): int
    {
        return $this->readingProgress()
            ->whereNotNull('completed_at')
            ->count()
            + $this->audioProgress()
                ->whereNotNull('completed_at')
                ->count();
    }

    // Obtenir le nombre de quiz réussis
    public function getPassedQuizzesCount(): int
    {
        return $this->quizAttempts()->where('is_passed', true)->count();
    }

    // Calculer les points de gamification
    public function getTotalPoints(): int
    {
        $booksPoints = $this->getCompletedBooksCount() * 10;
        $quizPoints = $this->quizAttempts()
            ->where('is_passed', true)
            ->sum('score');
        $badgePoints = $this->badges()->sum('points');

        return $booksPoints + $quizPoints + $badgePoints;
    }

    /**
     * Check if the user has the given role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the reading progress for a specific book.
     */
    public function getReadingProgressFor(Book $book): ?ReadingProgress
    {
        return $this->readingProgress()->where('book_id', $book->id)->first();
    }

    /**
     * Check if the user wants to receive a specific type of notification via a specific channel.
     *
     * @param  string  $type  The type of notification (e.g., 'new_message', 'quiz_result').
     * @param  string  $channel  The channel (e.g., 'email', 'site').
     */
    public function canReceiveNotification(string $type, string $channel): bool
    {
        // If preferences are not set, or if the specific preference is not found, default to true (opt-out model)
        if (empty($this->notification_preferences)) {
            return true;
        }

        // Check if the type exists in preferences
        if (! isset($this->notification_preferences[$type])) {
            return true;
        }

        // Check if the channel preference is explicitly set to false
        if (isset($this->notification_preferences[$type][$channel]) && $this->notification_preferences[$type][$channel] === false) {
            return false;
        }

        return true; // Default to true if not explicitly false
    }
}
