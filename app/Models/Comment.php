<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'parent_id',
        'commentable_type', // Bắt buộc để hỗ trợ polymorphic
        'commentable_id',   // Bắt buộc để hỗ trợ polymorphic
    ];

    /**
     * Lấy thông tin người dùng đã đăng bình luận.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Lấy danh sách bình luận con (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'replies');
    }

    /**
     * Mối quan hệ đa hình (polymorphic) - Bình luận có thể thuộc về nhiều loại đối tượng khác nhau.
     */
    public function commentable()
    {
        return $this->morphTo();
    }



    // protected static function boot()
    // {
    //     parent::boot();

    //     static::deleting(function ($comment) {
    //         $comment->replies->each(function ($reply) {
    //             $reply->delete();
    //         });
    //     });
    // }
}
