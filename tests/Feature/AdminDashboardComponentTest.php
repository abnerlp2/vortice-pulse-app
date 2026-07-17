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
    Livewire::test('admin-dashboard')
        ->assertStatus(200)
        ->assertSee('Dashboard de Organización')
        ->assertSee('Promedio Actual');
});

it('shows the offline ranking alert banner when the dashboard receives an alert', function () {
    Livewire::test('admin-dashboard')
        ->set('hasOfflineAlert', true)
        ->set('offlineAlert', [
            'message' => 'El orden consolidado del ranking cambió tras procesar propuestas offline.',
            'affected_talks' => ['talk-admin'],
        ])
        ->assertSee('Alerta: ranking alterado por datos offline tardíos')
        ->assertSee('El orden consolidado del ranking cambió tras procesar propuestas offline.')
        ->assertSee('talk-admin');
});
