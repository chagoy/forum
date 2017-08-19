<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ThreadHasNewReply
{
    use Dispatchable, SerializesModels;

    public $reply;

    /**
     * ThreadHasNewReply constructor.
     * @param $reply
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }


}
