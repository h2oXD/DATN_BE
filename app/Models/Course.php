<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;
    public $incrementing = false; // Không dùng auto-increment
    protected $keyType = 'string'; // Khóa chính là kiểu string (UUID)

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price_regular',
        'price_sale',
        'status',
        'is_free',

        'target_students',
        'learning_outcomes',
        'prerequisites',
        'who_is_this_for',

        'admin_commission_rate',
        'is_show_home',
        'thumbnail',
        'video_preview',
        'language',
        'level',
        'primary_content',

        'created_at',
        'updated_at',
        'submited_at',
        'censored_at',
        'admin_comment'
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'course_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    public function videos()
    {
        return $this->hasManyThrough(Video::class, Section::class, 'course_id', 'lesson_id', 'id', 'id');
    }

    public function documents()
    {
        return $this->hasManyThrough(Document::class, Section::class, 'course_id', 'lesson_id', 'id', 'id');
    }

    public function codings()
    {
        return $this->hasManyThrough(Coding::class, Section::class, 'course_id', 'lesson_id', 'id', 'id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'course_tag', 'course_id', 'tag_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }    
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    public function progresses()
    {
        return $this->hasMany(Progress::class, 'course_id');
    }
}
