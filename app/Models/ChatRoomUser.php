<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoomUser extends Model
{
    use HasFactory;

    protected $table = 'chat_room_users';
    public $timestamps = false;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
