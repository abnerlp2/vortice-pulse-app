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
        try {
            if ($ttlSeconds !== null) {
                Redis::setex($key, $ttlSeconds, $value);
            } else {
                Redis::set($key, $value);
            }
        } catch (\Throwable $e) {
            // Log or fallback gracefully if Redis service is unreachable
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
        try {
            return Redis::get($key);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Delete a key from Redis.
     *
     * @param string $key
     * @return int
     */
    public function delete(string $key): int
    {
        try {
            return (int) Redis::del($key);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Atomically increment a numeric value in Redis.
     *
     * @param string $key
     * @return int
     */
    public function increment(string $key): int
    {
        try {
            return (int) Redis::incr($key);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Atomically decrement a numeric value in Redis.
     *
     * @param string $key
     * @return int
     */
    public function decrement(string $key): int
    {
        try {
            return (int) Redis::decr($key);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
