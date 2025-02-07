<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecturer_id',
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
        'submited_at'
    ];


    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'course_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');

    }
}
