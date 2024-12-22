<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SampleEventB implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
