<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'profile_picture',
        'bio',
        'google_id',
        // 'status',
        'country',
        'province',
        'birth_date',
        'gender',
        'linkedin_url',
        'website_url',
        'certificate_file',
        'bank_name',
        'bank_nameUser',
        'bank_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function voucheruse()
    {
        return $this->hasMany(VoucherUse::class);
    }

    public function lecturerRegister()
    {
        return $this->hasOne(LecturerRegister::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }



    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_room_users', 'user_id', 'chat_room_id');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
