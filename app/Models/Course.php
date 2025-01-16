<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_students',
        'learning_outcomes',
        'prerequisites',
        'who_is_this_for',
        'price',
        'price_sale',
        'admin_commission_rate',
        'status',
        'is_show_home',
        'lecturer_id',
        'category_id',
        'thumbnail',
        'language',
        'level',
        'primary_content',
    ];
}
