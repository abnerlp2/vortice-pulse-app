<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ClearRedisState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse:clear-redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all vortice related state from Redis';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $keys = Redis::keys('vortice:*');

        if (!empty($keys)) {
            // Predis keys() might return prefixed keys depending on config,
            // but Redis::del accepts the names directly.
            // When using Laravel's Redis facade with phpredis, the prefix is omitted from the return value,
            // but with predis it might be included. To be safe, we just use the raw connection or map.
            
            // To ensure we don't have prefix issues, we just iterate or pass directly
            foreach ($keys as $key) {
                // If using a prefix, Laravel strips it in keys() return value (usually).
                // When using the Laravel facade, it automatically appends the prefix on del()
                // so we need to ensure we don't double-prefix if the driver already returned it.
                // The safest approach for both drivers without knowing the config is to use the raw underlying client,
                // or just call del() if the array is stripped.
                
                // For simplicity and alignment with the framework, we'll strip the default Laravel prefix if it exists
                // in the returned keys to avoid double-prefixing.
                $prefix = config('database.redis.options.prefix', 'laravel_database_');
                $strippedKey = str_starts_with($key, $prefix) ? substr($key, strlen($prefix)) : $key;
                
                Redis::del($strippedKey);
            }
        }

        $this->info('Estado de Redis limpiado exitosamente.');

        return 0;
    }
}
