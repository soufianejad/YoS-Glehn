<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'author_id',
        'category_id',
        'cover_image',
        'pdf_file',
        'audio_file',
        'pdf_pages',
        'audio_duration',
        'isbn',
        'published_year',
        'language',
        'space',
        'content_type',
        'pdf_price',
        'audio_price',
        'author_share',
        'platform_share',
        'status',
        'has_quiz',
        'is_downloadable',
        'is_featured',
    ];

    protected $casts = [
        'pdf_pages' => 'integer',
        'audio_duration' => 'integer',
        'published_year' => 'integer',
        'pdf_price' => 'float',
        'audio_price' => 'float',
    ];

    /**
     * Auteur du livre
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Catégorie du livre
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Tags associés
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'book_tag', 'book_id', 'tag_id');
    }

    /**
     * Progression de lecture (PDF)
     */
    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    /**
     * Progression audio
     */
    public function audioProgress(): HasMany
    {
        return $this->hasMany(AudioProgress::class);
    }

    /**
     * Avis/reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Revenus
     */
    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    /**
     * Assignments of the book
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(BookAssignment::class);
    }

    /**
     * Quizzes associated with the book
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Utilisateurs ayant mis ce livre en favori
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * Marque-pages pour ce livre
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Purchases of the book
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Nombre total de lectures
     */
    public function getReadsCountAttribute()
    {
        return $this->readingProgress()->sum('progress_percentage') ?? 0;
    }

    /**
     * Nombre total d'écoutes
     */
    public function getListensCountAttribute()
    {
        return $this->audioProgress()->sum('progress_percentage') ?? 0;
    }

    /**
     * Revenu total généré par le livre
     */
    public function getTotalRevenue()
    {
        return $this->revenues()->sum('total_amount');
    }

    /**
     * Revenu de l'auteur pour ce livre
     */
    public function getAuthorRevenue()
    {
        return $this->revenues()->sum('author_amount');
    }

    /**
     * Moyenne des notes
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    /**
     * Get the full path to the private PDF file.
     */
    public function getPrivatePdfPathAttribute(): ?string
    {
        if ($this->pdf_file) {
            // Assuming pdf_file stores just the filename or a relative path within private_pdfs
            return 'private_pdfs/'.$this->pdf_file;
        }

        return null;
    }

    /**
     * Get the author's revenue percentage for the book.
     *
     * The logic is as follows:
     * 1. If a specific `author_share` is set on the book, use it.
     * 2. If not, try to get the global default from the settings table.
     * 3. If no global setting, fall back to the old logic based on `content_type`.
     */
    public function getAuthorRevenuePercentageAttribute(): int
    {
        if (! is_null($this->author_share)) {
            return $this->author_share;
        }

        $globalShare = cache()->remember('setting.default_author_share', 3600, function () {
            return Setting::where('key', 'platform.default_author_share')->value('value');
        });

        if (! is_null($globalShare)) {
            return (int) $globalShare;
        }

        // Fallback to old logic
        return $this->content_type === 'produced' ? 60 : 80;
    }

    /**
     * Get the platform's revenue percentage for the book.
     *
     * The logic is as follows:
     * 1. If a specific `platform_share` is set on the book, use it.
     * 2. If not, calculate it based on the author's share.
     */
    public function getPlatformRevenuePercentageAttribute(): int
    {
        if (! is_null($this->platform_share)) {
            return $this->platform_share;
        }

        return 100 - $this->getAuthorRevenuePercentageAttribute();
    }

    /**
     * Get the full URL for the book's cover image.
     */
    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image
            ? asset('storage/'.$this->cover_image)
            : asset('images/default_book_cover.png'); // Ensure you have a default image at this path
    }
}
