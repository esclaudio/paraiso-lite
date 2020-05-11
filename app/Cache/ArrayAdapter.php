<?php

namespace App\Cache;

class ArrayAdapter implements CacheInterface
{
    protected $storage = [];

    public function get(string $key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }
    }

    public function getAll(string $pattern)
    {
        //TODO: Implement
        return $this->storage;
    }

    public function put(string $key, string $value, int $minutes = null)
    {
        $this->storage[$key] = $value;
    }

    public function forever(string $key, string $value)
    {
        $this->put($key, $value, 0);
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
        unset($this->storage[$key]);
        return true;
    }

    public function forgetAll(string $pattern)
    {
        //TODO: Implement
        $this->storage = [];
        return true;
    }
}