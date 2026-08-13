<?php

use Carbon\Carbon;
use App\Models\TimeBlock;
use App\Models\Talk;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('shows block and talks until end_time +29 minutes and hides after +31 minutes', function () {
    $end = Carbon::parse('2026-08-12 10:00:00');

    $block = TimeBlock::create([
        'id' => 'block-visibility',
        'start_time' => $end->copy()->subHour(),
        'end_time' => $end,
    ]);

    $talk = Talk::create([
        'id' => 'talk-visibility',
        'time_block_id' => $block->id,
        'title' => 'Visibility Test Talk',
        'speaker' => 'Speaker V',
        'start_time' => $end->copy()->subMinutes(45),
        'end_time' => $end->copy()->subMinutes(30),
    ]);

    Carbon::setTestNow($end->copy()->addMinutes(29));

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Visibility Test Talk');

    Carbon::setTestNow($end->copy()->addMinutes(31));

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertDontSee('Visibility Test Talk');
    Carbon::setTestNow();
});

it('orders talks: active first, then recently ended within 30 minutes', function () {
    $now = Carbon::parse('2026-08-12 10:00:00');

    $block = TimeBlock::create([
        'id' => 'block-order',
        'start_time' => $now->copy()->subHour(),
        'end_time' => $now->copy()->addMinutes(30),
    ]);

    $activeTalk = Talk::create([
        'id' => 'active-talk',
        'time_block_id' => $block->id,
        'title' => 'Active Talk',
        'speaker' => 'Speaker A',
        'start_time' => $now->copy()->subMinutes(10),
        'end_time' => $now->copy()->addMinutes(15),
    ]);

    $recentlyEnded = Talk::create([
        'id' => 'recent-talk',
        'time_block_id' => $block->id,
        'title' => 'Recently Ended',
        'speaker' => 'Speaker B',
        'start_time' => $now->copy()->subHour(),
        'end_time' => $now->copy()->subMinutes(10),
    ]);

    Carbon::setTestNow($now);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSeeInOrder(['Active Talk', 'Recently Ended']);
    Carbon::setTestNow();
});

it('selects current block over grace blocks when overlapping at 00:30', function () {
    Carbon::setTestNow('2026-08-13 00:30:00');

    $block1 = \App\Models\TimeBlock::create([
        'id' => 'block-1',
        'start_time' => Carbon::parse('2026-08-13 00:00:00'),
        'end_time' => Carbon::parse('2026-08-13 00:20:00'),
    ]);

    $block2 = \App\Models\TimeBlock::create([
        'id' => 'block-2',
        'start_time' => Carbon::parse('2026-08-13 00:25:00'),
        'end_time' => Carbon::parse('2026-08-13 00:45:00'),
    ]);

    \App\Models\Talk::create([
        'id' => 'talk-1',
        'time_block_id' => $block1->id,
        'title' => 'Talk Block 1',
        'speaker' => 'S1',
        'start_time' => $block1->start_time,
        'end_time' => $block1->end_time,
    ]);

    \App\Models\Talk::create([
        'id' => 'talk-2',
        'time_block_id' => $block2->id,
        'title' => 'Talk Block 2',
        'speaker' => 'S2',
        'start_time' => $block2->start_time,
        'end_time' => $block2->end_time,
    ]);

    Livewire::test(\App\Livewire\ActiveAgendaLanding::class)
        ->assertSet('currentBlock.id', 'block-2')
        ->assertSet('graceBlocks.0.id', 'block-1');

    Carbon::setTestNow();
});
