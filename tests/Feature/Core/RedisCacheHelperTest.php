<?php

use App\Core\Evaluation\Services\RedisCacheHelper;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // Limpiamos la base de datos de Redis antes de cada prueba para asegurar asilamiento
    Redis::flushdb();
});

it('can write and read a dynamic value from redis', function () {
    $helper = new RedisCacheHelper();
    
    $helper->set('dynamic_test_key', 'test_value');
    $value = $helper->get('dynamic_test_key');
    
    expect($value)->toBe('test_value');
});

it('can atomically increment and decrement values', function () {
    $helper = new RedisCacheHelper();
    
    $helper->increment('counter_test_key');
    $helper->increment('counter_test_key');
    
    $count = $helper->get('counter_test_key');
    expect((int) $count)->toBe(2);
    
    $helper->decrement('counter_test_key');
    $count = $helper->get('counter_test_key');
    expect((int) $count)->toBe(1);
});

it('can set expiration for a key', function () {
    $helper = new RedisCacheHelper();
    
    $helper->set('expiring_test_key', 'value', 10); // Expira en 10 segundos
    
    expect($helper->get('expiring_test_key'))->toBe('value');
    
    // Verificamos el tiempo de vida (TTL) directamente desde el Facade de Redis
    $ttl = Redis::ttl('expiring_test_key');
    expect($ttl)->toBeGreaterThan(0)->toBeLessThanOrEqual(10);
});
