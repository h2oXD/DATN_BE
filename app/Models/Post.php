<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'thumbnail',
        'status',
        'views',
    ];

    /**
     * Một bài viết thuộc về một người dùng (tác giả).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Một bài viết có nhiều bình luận (dùng quan hệ đa hình).
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


    

    /**
     * Tự động cập nhật slug khi tạo bài viết.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            $post->slug = str()->slug($post->title);
        });


        // static::deleting(function ($post) {
        //     // Xóa tất cả các comment liên quan trước khi xóa bài viết
        //     $post->comments->each(function ($comment) {
        //         $comment->delete();
        //     });
        // });
    }
}
