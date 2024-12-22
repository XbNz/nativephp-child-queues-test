<?php

namespace App\Listeners;

use App\Events\SampleEvent;
use App\Events\SampleEventB;

class SampleListener
{
    public function __construct() {}

    public function handle(SampleEvent $event): void
    {
        event(new SampleEventB());
    }
}
