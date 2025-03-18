<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewable_id',
        'reviewable_type',
        'rating',
        'review_text',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }
}
