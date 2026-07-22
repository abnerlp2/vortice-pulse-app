<?php

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->timeBlock = TimeBlock::create([
        'id' => 'block-offline-ui',
        'start_time' => now()->subMinutes(20),
        'end_time' => now()->addMinutes(20),
    ]);

    $this->talk = Talk::create([
        'id' => 'talk-offline-ui',
        'title' => 'Offline UI Test Talk',
        'speaker' => 'Carla Méndez',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(10),
    ]);
});

it('renders the mobile evaluator with offline storage UI hooks and pending indicator template', function () {
    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->assertStatus(200)
        ->assertSee('x-data="evaluatorDevice()"', false)
        ->assertSee('Envío pendiente por conexión')
        ->assertSee('Reintentar sincronización offline');
});
