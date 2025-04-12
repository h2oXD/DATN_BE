<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;
    public $incrementing = false; // Không dùng auto-increment
    protected $keyType = 'string'; // Khóa chính là kiểu string (UUID)

    protected $fillable = [
        // 'course_id',
        'section_id',
        'title',
        'description',
        'order',
        'type',
        'is_preview'
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Gán UUID nếu chưa có
            }
        });
    }

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

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'lesson_id');
    }
}
