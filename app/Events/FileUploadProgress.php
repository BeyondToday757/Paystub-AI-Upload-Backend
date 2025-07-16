<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileUploadProgress implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $uuid;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($uuid, $message)
    {
        $this->uuid = $uuid;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel('upload-progress')];
    }

    public function broadCastAs()
    {
        return 'FileUploadProgress';
    }

    public function broadcastWith()
    {
        return [
            'uuid' => $this->uuid,
            'message' => $this->message,
        ];
    }
}
