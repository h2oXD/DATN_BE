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
        return $this->hasMany(Document::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function codings()
    {
        return $this->hasMany(Coding::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
