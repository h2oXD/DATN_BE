<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutedUser extends Model
{
    use HasFactory;


    protected $table = 'muted_users';

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'muted_until',
    ];

    protected $casts = [
        'muted_until' => 'datetime',
    ];

    /**
     * Liên kết với bảng ChatRoom
     */
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    /**
     * Liên kết với bảng User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra xem người dùng có bị mute không
     */
    public function isMuted()
    {
        return $this->muted_until && Carbon::now()->lessThan($this->muted_until);
    }
}
