<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coding extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'language',
        'problem_title',
        'problem_description',
        'starter_code',
        'solution_code',
        'test_cases',
    ];
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
