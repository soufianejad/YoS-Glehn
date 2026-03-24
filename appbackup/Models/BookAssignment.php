<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'class_id',
        'school_id',
        'assigned_at',
        'due_date',
        'is_mandatory',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'due_date' => 'date',
        'is_mandatory' => 'boolean',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
