<?php

namespace App\Cache;

interface CacheInterface
{
    public function get(string $key);
    public function getAll(string $pattern);
    public function put(string $key, string $value, int $minutes = null);
    public function forever(string $key, string $value);
    public function remember(string $key, int $minutes = null, callable $callback);
    public function forget(string $key);
    public function forgetAll(string $pattern);
}