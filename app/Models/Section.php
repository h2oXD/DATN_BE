<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Section extends Model
{
    use HasFactory;
    public $incrementing = false; // Không dùng auto-increment
    protected $keyType = 'string'; // Khóa chính là kiểu string (UUID)

    protected $fillable = [
        'course_id',
        'id',
        'title',
        'description',
        'order',
        'total_lessons'
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

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
