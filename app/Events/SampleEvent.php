<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SampleEvent implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
