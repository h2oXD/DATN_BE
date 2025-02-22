<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'description',
        'order',
        'type',
        'is_preview'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function documents()
    {
        return $this->hasOne(Document::class);
    }

    public function videos()
    {
        return $this->hasOne(Video::class);
    }

    public function codings()
    {
        return $this->hasOne(Coding::class);
    }

    public function quizzes() {
        return $this->belongsToMany(Quiz::class, 'lesson_quiz');
    }
}
