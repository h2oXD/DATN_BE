<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'price_sale',
        'status',

        'target_students',
        'learning_outcomes',
        'prerequisites',
        'who_is_this_for',

        'admin_commission_rate',
        'is_show_home',
        'thumbnail',
        'language',
        'level',
        'primary_content',
        
        'created_at',
        'updated_at',
        'submited_at',
        'censored_at',
        'admin_comment'
    ];

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
}
