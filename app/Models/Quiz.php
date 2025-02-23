<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'title',
        'lesson_id',
    ];
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function questionsWithAnswers()
    {
        return $this->hasMany(Question::class)->with('answers');
    }
}
