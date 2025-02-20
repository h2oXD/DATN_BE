<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'image_url',
        'is_multiple_choice',
        'correct_answers',
        'order',
    ];
    public function quiz() {
        return $this->belongsTo(Quiz::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }


}
