<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'quiz_id',
        'student_id',
        'score',
        'total_questions',
        'correct_answers',
    ];
}
