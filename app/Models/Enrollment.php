<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'enrolled_at',
        'completed_at',
    ];
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function progress()
    {
        return $this->hasOne(Progress::class)
            ->whereColumn('progress.user_id', 'enrollments.user_id')
            ->whereColumn('progress.course_id', 'enrollments.course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
