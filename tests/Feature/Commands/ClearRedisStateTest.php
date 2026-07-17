<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

uses(TestCase::class);

it('clears all vortice related state from redis', function () {
    // Arrange: Simulate keys in Redis
    Redis::set('vortice:signatures:talk-1:hash123', 'true');
    Redis::set('vortice:time_blocks:active', 'block-1');

    // Act: Run the clear-redis command
    $exitCode = Artisan::call('pulse:clear-redis');

    // Assert: Check success exit code and console output
    expect($exitCode)->toBe(0);
    expect(Artisan::output())->toContain('Estado de Redis limpiado exitosamente');

    // Assert: Verify keys were deleted
    expect(Redis::keys('vortice:signatures:talk-1:hash123'))->toBeEmpty();
    expect(Redis::keys('vortice:time_blocks:active'))->toBeEmpty();
});
