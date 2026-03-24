<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'language',
        'file_type',
        'path',
        'pages',
        'duration',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
