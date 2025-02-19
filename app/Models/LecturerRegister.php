<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerRegister extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'answer1',
        'answer2',
        'answer3',
        'admin_rejection_reason',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'lecturer_answers' => 'json',
    ];

    /**
     * Get the user that owns the lecturer register.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}