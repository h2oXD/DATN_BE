<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_url',
        'issued_at',
    ];
    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function progress()
    {
        return $this->hasOne(Progress::class, 'user_id', 'user_id')
                    ->whereColumn('course_id', 'course_id')
                    ->where('status', 'completed');
    }
}
