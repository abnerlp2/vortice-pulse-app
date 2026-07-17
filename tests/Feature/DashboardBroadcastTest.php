<?php

use App\Core\Evaluation\Events\EvaluationReceived;
use App\Core\Evaluation\Services\EvaluationService;
use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->timeBlock = TimeBlock::create([
        'id' => 'block-broadcast',
        'start_time' => now()->subMinutes(20),
        'end_time' => now()->addMinutes(20),
    ]);

    $this->talk = Talk::create([
        'id' => 'talk-broadcast',
        'title' => 'Potenciando UX Realtime',
        'speaker' => 'María Gómez',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(10),
    ]);
});

it('broadcasts an EvaluationReceived event when a vote is registered', function () {
    Event::fake([
        EvaluationReceived::class,
    ]);

    $signature = hash('sha256', 'broadcast-device-1');

    app(EvaluationService::class)->registerVote(
        $this->talk->id,
        5,
        $signature,
        'Excelente enfoque',
        'Más interactividad'
    );

    Event::assertDispatched(EvaluationReceived::class, function ($event) use ($signature) {
        return $event->talkId === 'talk-broadcast'
            && $event->rating === 5
            && $event->average === 5.0
            && $event->totalVotes === 1;
    });
});
