<?php

use App\Core\Evaluation\Services\EvaluationService;
use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->timeBlock = TimeBlock::create([
        'id' => 'block-active',
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(30),
    ]);

    $this->talk = Talk::create([
        'id' => 'talk-active',
        'title' => 'TDD en el Mundo Real',
        'speaker' => 'Juan Pérez',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(5),
        'end_time' => now()->addMinutes(25),
    ]);
});

it('renders the vortice console component successfully', function () {
    Livewire::test('vortice-console', ['talkId' => $this->talk->id])
        ->assertStatus(200);
});

it('displays the average and standard deviation keys from the service', function () {
    $service = app(EvaluationService::class);
    
    // Inyectamos algunos votos para que el servicio no devuelva promedios en 0.0
    // Promedio: 4.5, Votos: 2, Varianza: 0.25, Desviación Estándar: 0.5 (Unificado)
    $service->registerVote($this->talk->id, 5, hash('sha256', 'device-1'));
    $service->registerVote($this->talk->id, 4, hash('sha256', 'device-2'));

    Livewire::test('vortice-console', ['talkId' => $this->talk->id])
        ->assertSee('4.5') // Promedio esperado
        ->assertSee('0.5'); // Desviación Estándar esperada
});

it('displays polarized states based on high standard deviation', function () {
    $service = app(EvaluationService::class);
    
    // Inyectamos votos extremos para forzar una desviación estándar alta (> 1.2)
    // Promedio: 3.0, Votos: 2, Varianza: 4.0, Desviación Estándar: 2.0 (Debate activo / Polarizado)
    $service->registerVote($this->talk->id, 5, hash('sha256', 'device-3'));
    $service->registerVote($this->talk->id, 1, hash('sha256', 'device-4'));

    Livewire::test('vortice-console', ['talkId' => $this->talk->id])
        ->assertSee('3') // Promedio esperado
        ->assertSee('2'); // Desviación Estándar esperada
});
