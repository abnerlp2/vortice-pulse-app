<?php

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->timeBlock = TimeBlock::create([
        'id' => 'block-admin',
        'start_time' => now()->subMinutes(20),
        'end_time' => now()->addMinutes(20),
    ]);

    $this->talk = Talk::create([
        'id' => 'talk-admin',
        'title' => 'Admin Dashboard Realtime',
        'speaker' => 'Lucía Ramos',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(10),
    ]);
});

it('renders the admin dashboard component successfully', function () {
    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->assertStatus(200)
        ->assertSee('Dashboard de Organización')
        ->assertSee('Promedio Actual');
});

it('applies live event updates and displays a ranking alert banner', function () {
    $component = Livewire::test(\App\Livewire\AdminDashboard::class);

    $component->call('onEvaluationReceived', [
        'talk_id' => $this->talk->id,
        'time_block_id' => $this->timeBlock->id,
        'average' => 4.7,
        'total_votes' => 12,
    ])
    ->assertSet("talkStats.{$this->talk->id}.average", 4.7)
    ->assertSet("talkStats.{$this->talk->id}.total_votes", 12);

    $component->call('onRankingOrderAltered', [
        'message' => 'Orden alterado por sincronización offline.',
        'after_order' => [$this->talk->id],
        'affected_talks' => [$this->talk->id],
    ])
    ->assertSet('hasOfflineAlert', true)
    ->assertSet('offlineAlert.message', 'Orden alterado por sincronización offline.')
    ->assertSee('Alerta: ranking alterado por datos offline tardíos')
    ->assertSee('Orden alterado por sincronización offline.')
    ->assertSee('Admin Dashboard Realtime');
});

it('shows the offline ranking alert banner when the dashboard receives an alert', function () {
    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->set('hasOfflineAlert', true)
        ->set('offlineAlert', [
            'message' => 'El orden consolidado del ranking cambió tras procesar propuestas offline.',
            'affected_talks' => ['talk-admin'],
        ])
        ->assertSee('Alerta: ranking alterado por datos offline tardíos')
        ->assertSee('El orden consolidado del ranking cambió tras procesar propuestas offline.')
        ->assertSee('Admin Dashboard Realtime');
});
