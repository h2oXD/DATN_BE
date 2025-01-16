<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'lecturer_id',
        'amount',
        'type',
        'status',
        'payment_method',
        'transaction_date',
        'reference_id',
    ];
}
