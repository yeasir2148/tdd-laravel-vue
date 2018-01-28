<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewReplyAddedToThread
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $reply;
    public $thread;
    public $mentioned_users;


    public function __construct($thread, $reply, $mentioned_users)
    {
        $this->reply = $reply;
        $this->thread = $thread;
        $this->mentioned_users = $mentioned_users;
    }

}
