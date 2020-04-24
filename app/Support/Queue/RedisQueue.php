<?php

namespace App\Support\Queue;

use Enqueue\Redis\RedisConnectionFactory;
use App\Queue\Contracts\ShouldQueue;

final class RedisQueue extends Queue
{
    protected $connection;
    
    protected $default;

    public function __construct(array $config, string $default = 'default')
    {
        $this->connection = new RedisConnectionFactory($config);
        $this->default = $default;
    }

    public function dispatch(ShouldQueue $job, string $queue = null)
    {
        $context = $this->connection->createContext();

        $destination = $context->createQueue($queue ?: $this->default);
        $message = $context->createMessage(serialize(clone $job));

        $context->createProducer()->send($destination, $message);
    }
}