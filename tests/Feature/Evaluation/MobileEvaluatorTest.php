<?php

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Preparar datos base para que el componente tenga un Talk válido que evaluar
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

it('renders the talk time range in the evaluator header', function () {
    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->assertStatus(200)
        ->assertSee($this->talk->formatted_start_time . ' - ' . $this->talk->formatted_end_time);
});

it('renders the mobile evaluator component successfully', function () {
    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->assertStatus(200);
});

it('persists a valid evaluation with a device signature', function () {
    $signature = hash('sha256', 'mock-device-uuid-1');

    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->set('rating', 5)
        ->set('deviceSignature', $signature)
        ->call('submitEvaluation')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('evaluations', [
        'talk_id' => $this->talk->id,
        'rating' => 5,
        'device_signature' => $signature,
    ]);
});

it('persists a valid evaluation with qualitative aspects', function () {
    $signature = hash('sha256', 'mock-device-uuid-qualitative');

    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->set('rating', 5)
        ->set('deviceSignature', $signature)
        ->set('likedAspects', 'Excelente contenido')
        ->set('improvementAspects', 'Más tiempo para Q&A')
        ->call('submitEvaluation')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('evaluations', [
        'talk_id' => $this->talk->id,
        'rating' => 5,
        'device_signature' => $signature,
        'liked_aspects' => 'Excelente contenido',
        'improvement_aspects' => 'Más tiempo para Q&A',
    ]);
});

it('rejects evaluation submission if device signature is missing', function () {
    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->set('rating', 4)
        ->set('deviceSignature', null)
        ->call('submitEvaluation')
        ->assertHasErrors(['deviceSignature']);

    $this->assertDatabaseEmpty('evaluations');
});

it('prevents duplicate votes for the same talk using the same device signature', function () {
    $signature = hash('sha256', 'mock-device-uuid-2');

    // Emitimos el primer voto válido
    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->set('rating', 4)
        ->set('deviceSignature', $signature)
        ->call('submitEvaluation')
        ->assertHasNoErrors();

    // Intentamos emitir un segundo voto para la MISMA charla con la MISMA firma
    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->set('rating', 5)
        ->set('deviceSignature', $signature)
        ->call('submitEvaluation')
        // Esperamos que falle la validación de unicidad o salte un error controlado
        ->assertHasErrors(['deviceSignature']);

    // Afirmamos en base de datos que solo se registró un voto (el primero)
    $this->assertDatabaseCount('evaluations', 1);
    $this->assertDatabaseHas('evaluations', [
        'talk_id' => $this->talk->id,
        'rating' => 4, // Asegura que el primer rating es el que quedó guardado
        'device_signature' => $signature,
    ]);
});

it('redirects to landing if accessed before the talk starts', function () {
    $this->travelTo($this->timeBlock->start_time->copy()->subMinute());

    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->assertRedirect(route('landing'));
});

it('redirects to landing if accessed more than 30 minutes after the talk ends', function () {
    $this->travelTo($this->timeBlock->end_time->copy()->addMinutes(31));

    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->assertRedirect(route('landing'));
});

it('accepts an evaluation submitted exactly during the talk time block or within the 30 min tolerance', function () {
    // Viajamos exactamente a 15 minutos después de finalizado el bloque (dentro de la tolerancia)
    $this->travelTo($this->timeBlock->end_time->copy()->addMinutes(15));

    $signature = hash('sha256', 'mock-device-on-time');

    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->set('rating', 4)
        ->set('deviceSignature', $signature)
        ->call('submitEvaluation')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('evaluations', [
        'talk_id' => $this->talk->id,
        'rating' => 4,
        'device_signature' => $signature,
    ]);
});

it('renders back to agenda button on active rating view and success state', function () {
    $signature = hash('sha256', 'mock-device-uuid-back-btn');

    // Active rating state
    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
    ->assertSee('Volver a la agenda')
        ->assertSee('/')
        // Success state
        ->set('rating', 5)
        ->set('deviceSignature', $signature)
        ->call('submitEvaluation')
    ->assertSee('Volver a la agenda')
        ->assertSee('/');
});

it('renders sticky header, textarea styles and mobile container constraints', function () {
    $this->get(route('talk.show', $this->talk->id))
        ->assertSee('vortice-logo.svg')
        ->assertSee('sticky top-0')
        ->assertSee('max-w-md');

    Livewire::test('mobile-evaluator', ['talkId' => $this->talk->id])
        ->assertSee('Volver a la agenda')
        ->assertSee('rounded-xl p-3');
});
