<?php

namespace App\Events;

use App\Models\LecturerRegister;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LecturerRegisterRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lecturerRegister;

    /**
     * Create a new event instance.
     */
    public function __construct(LecturerRegister $lecturerRegister)
    {
        $this->lecturerRegister = $lecturerRegister;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
