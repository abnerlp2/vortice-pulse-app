<?php

namespace App\Core\Evaluation\Services;

use Illuminate\Support\Facades\Redis;

class RedisCacheHelper
{
    /**
     * Store a value in Redis, optionally with an expiration time in seconds.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttlSeconds
     * @return void
     */
    public function set(string $key, mixed $value, ?int $ttlSeconds = null): void
    {
        if ($ttlSeconds !== null) {
            Redis::setex($key, $ttlSeconds, $value);
        } else {
            Redis::set($key, $value);
        }
    }

    /**
     * Retrieve a value from Redis by its key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return Redis::get($key);
    }

    /**
     * Delete a key from Redis.
     *
     * @param string $key
     * @return int
     */
    public function delete(string $key): int
    {
        return Redis::del($key);
    }

    /**
     * Atomically increment a numeric value in Redis.
     *
     * @param string $key
     * @return int
     */
    public function increment(string $key): int
    {
        return Redis::incr($key);
    }

    /**
     * Atomically decrement a numeric value in Redis.
     *
     * @param string $key
     * @return int
     */
    public function decrement(string $key): int
    {
        return Redis::decr($key);
    }
}
