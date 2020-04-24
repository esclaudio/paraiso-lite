<?php

namespace App\Cache;

use Predis\Client;

class RedisAdapter implements CacheInterface
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(string $key)
    {
        return $this->client->get($key);
    }

    public function getAll(string $pattern)
    {
        return $this->client->keys($pattern);
    }

    public function put(string $key, string $value, int $minutes = null)
    {
        if ($minutes === null) {
            return $this->forever($key, $value);
        }

        return $this->client->setex($key, (int) max(1, $minutes * 60), $value);
    }

    public function forever(string $key, string $value)
    {
        return $this->client->set($key, $value);
    }

    public function remember(string $key, int $minutes = null, callable $callback)
    {
        $value = $this->get($key);
        
        if (!is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $minutes);

        return $value;
    }

    public function forget(string $key)
    {
        return $this->client->del($key);
    }

    public function forgetAll(string $pattern)
    {
        $keys = $this->client->keys($pattern);

        foreach ($keys as $key) {
            $this->client->del($key);
        }
    }
}