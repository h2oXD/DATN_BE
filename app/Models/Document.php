<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'document_url',
        'file_type',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
