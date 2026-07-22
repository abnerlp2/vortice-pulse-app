<?php

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->timeBlock = TimeBlock::create([
        'id' => 'block-offline',
        'start_time' => now()->subMinutes(20),
        'end_time' => now()->addMinutes(20),
    ]);

    $this->talk = Talk::create([
        'id' => 'talk-active',
        'title' => 'Offline Sync Demo',
        'speaker' => 'Andrés Nuñez',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(10),
    ]);
});

it('simulates offline sync and persists pending evaluations', function () {
    $exitCode = Artisan::call('pulse:simulate-offline-sync', [
        'time_block_id' => $this->timeBlock->id,
        '--batch' => 10,
    ]);

    expect($exitCode)->toBe(0);
    expect(DB::table('evaluations')->count())->toBe(10);
});
