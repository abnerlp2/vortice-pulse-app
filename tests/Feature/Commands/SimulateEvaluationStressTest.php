<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('simulates massive concurrent evaluations for a specific talk', function () {
    // Arrange: Create necessary parent records directly in DB
    $timeBlockId = 'block-1';
    $talkId = 'talk-1';

    DB::table('time_blocks')->insert([
        'id' => $timeBlockId,
        'start_time' => '2026-07-16 09:00:00',
        'end_time' => '2026-07-16 10:00:00',
    ]);

    DB::table('talks')->insert([
        'id' => $talkId,
        'title' => 'Opening Keynote',
        'speaker' => 'Ana López',
        'time_block_id' => $timeBlockId,
        'start_time' => '2026-07-16 09:00:00',
        'end_time' => '2026-07-16 09:45:00',
    ]);

    // Act: Run the simulate-stress command with 300 concurrent evaluations
    $exitCode = Artisan::call('pulse:simulate-stress', [
        'talk_id' => $talkId,
        '--concurrent' => 300,
    ]);

    // Assert: Check success exit code
    expect($exitCode)->toBe(0);

    // Assert: Verify exactly 300 evaluations were created
    $evaluationsCount = DB::table('evaluations')->where('talk_id', $talkId)->count();
    expect($evaluationsCount)->toBe(300);

    // Assert: Verify all 300 evaluations have unique device signatures
    $uniqueSignaturesCount = DB::table('evaluations')
        ->where('talk_id', $talkId)
        ->distinct('device_signature')
        ->count('device_signature');
    expect($uniqueSignaturesCount)->toBe(300);
});
