<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Only broadcast to the receiver's channel to prevent duplicates
        return new Channel('chat.' . $this->message->receiver_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'MessageSent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => array_merge($this->message->toArray(), [
                'sender_name' => $this->message->sender->name,
                'created_at' => $this->message->created_at->toISOString(),
                'formatted_time' => $this->message->created_at->format('g:i A')
            ])
        ];
    }
}
