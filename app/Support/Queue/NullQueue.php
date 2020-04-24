<?php

namespace App\Support\Queue;

use Enqueue\Redis\RedisConnectionFactory;
use App\Queue\Contracts\ShouldQueue;

final class NullQueue extends Queue
{
    public function dispatch(ShouldQueue $job, string $queue = null)
    {
        //
    }
}