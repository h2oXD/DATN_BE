<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'expertise',
        'rating',
        'achievements',
        'certifications',
        'linkedin_url',
        'website_url',
        'total_reviews',
        'total_courses',
        'total_students',
    ];
}
