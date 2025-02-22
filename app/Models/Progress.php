<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress_percent',
    ];
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
     public function certificate()
    {
        return $this->hasOne(Certificate::class, 'user_id', 'user_id')
                    ->whereColumn('course_id', 'course_id');
    }
}
