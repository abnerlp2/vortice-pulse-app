<?php

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns formatted start and end time in twelve hour am/pm format', function () {
    TimeBlock::create([
        'id' => 'block-1',
        'start_time' => '2026-08-12 09:00:00',
        'end_time' => '2026-08-12 10:00:00',
    ]);

    $talk = Talk::create([
        'id' => 'talk-formatted-time',
        'time_block_id' => 'block-1',
        'title' => 'Talk Format',
        'speaker' => 'Expositor',
        'start_time' => '2026-08-12 09:15:00',
        'end_time' => '2026-08-12 09:45:00',
    ]);

    expect($talk->formatted_start_time)->toBe('9:15 am');
    expect($talk->formatted_end_time)->toBe('9:45 am');
});
