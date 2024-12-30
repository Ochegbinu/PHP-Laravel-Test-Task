<?php

namespace App\Events;

use App\Models\Brt;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BrtCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

     public $brt;

    public function __construct(Brt $brt)
    {
        $this->brt = $brt;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('brt.' . $this->brt->user_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'brt_code' => $this->brt->brt_code,
            'reserved_amount' => $this->brt->reserved_amount,
            'status' => $this->brt->status,
        ];
    }
}
