<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['user_id', 'video_id', 'content', 'timestamp'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    // Accessor để định dạng timestamp
    public function getTimestampAttribute($value)
    {
        $minutes = floor($value / 60);
        $seconds = $value % 60;
        return sprintf('%d:%02d', $minutes, $seconds); // Định dạng MM:SS
    }

    // Mutator để xử lý dữ liệu đầu vào
    public function setTimestampAttribute($value)
    {
        // Nếu đầu vào là chuỗi "MM:SS", chuyển thành giây
        if (is_string($value) && strpos($value, ':') !== false) {
            list($minutes, $seconds) = explode(':', $value);
            $this->attributes['timestamp'] = ($minutes * 60) + $seconds;
        } else {
            $this->attributes['timestamp'] = $value; // Giữ nguyên nếu là số
        }
    }
}
