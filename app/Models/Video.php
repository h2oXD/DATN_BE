<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'video_url',
        'duration',
        'created_at',
        'updated_at',
    ];


    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
