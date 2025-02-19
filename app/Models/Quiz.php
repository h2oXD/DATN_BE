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
        'description',
    ];
    public function lessons() {
        return $this->belongsToMany(Lesson::class, 'lesson_quiz');
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }
}
