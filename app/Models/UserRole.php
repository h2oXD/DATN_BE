<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'user_role';
    protected $fillable = [
        'user_id',
        'role_id',
    ];
}
