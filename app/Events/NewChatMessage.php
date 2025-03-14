<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $message;
    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return
            [
                new Channel('chat-room.' . $this->message->chat_room_id),
            ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'chat_room_id' => $this->message->chat_room_id,
            'user_id' => $this->message->user_id,
            'message' => $this->message->message,
            'file_path' => $this->message->file_path,
            'created_at' => $this->message->created_at->toISOString(), // Định dạng ISO 8601
            'user' => [
                'id' => $this->message->user->id,
                'name' => $this->message->user->name,
                'profile_picture' => $this->message->user->profile_picture,
            ],
        ];
    }
}
