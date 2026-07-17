<?php

use App\Core\Evaluation\Services\EvaluationService;
use App\Models\Evaluation;
use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Redis::flushdb();

    // Setup de datos mínimos
    $this->timeBlock = TimeBlock::create([
        'id' => 'block-test',
        'start_time' => now()->subMinutes(30),
        'end_time' => now()->addMinutes(30),
    ]);

    $this->talk = Talk::create([
        'id' => 'talk-test-pulse',
        'title' => 'Pulse Engine Talk',
        'speaker' => 'Dr. Metric',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(15),
        'end_time' => now()->addMinutes(15),
    ]);

    $this->service = app(EvaluationService::class);
});

it('calculates the exact arithmetic mean and standard deviation for a talk', function () {
    // Inyectamos 5 votos conocidos: 5, 4, 4, 3, 5. 
    // Promedio: 4.2
    // Desviación Estándar poblacional: ~0.7483
    $ratings = [5, 4, 4, 3, 5];
    
    foreach ($ratings as $index => $rating) {
        $this->service->registerVote(
            $this->talk->id,
            $rating,
            hash('sha256', "device-{$index}")
        );
    }

    $stats = $this->service->getTalkStatistics($this->talk->id);

    expect($stats)->toBeArray();
    expect($stats['average'])->toEqual(4.2);
    expect(round($stats['standard_deviation'], 4))->toEqual(0.7483);
    expect($stats['total_votes'])->toEqual(5);
});

it('returns zero standard deviation when there are less than two evaluations to prevent division by zero', function () {
    // Escenario: Un solo voto
    $this->service->registerVote(
        $this->talk->id,
        5,
        hash('sha256', "device-solo")
    );

    $stats = $this->service->getTalkStatistics($this->talk->id);

    expect($stats['average'])->toEqual(5.0);
    expect($stats['standard_deviation'])->toEqual(0.0);
    expect($stats['total_votes'])->toEqual(1);
});

it('caches the statistical results in Redis and invalidates them upon new evaluation', function () {
    $redisKey = "vortice:pulse:talk:{$this->talk->id}";

    // Voto 1
    $this->service->registerVote($this->talk->id, 4, hash('sha256', "device-a"));
    
    // Al solicitar estadísticas, el servicio debería hacer cache del cálculo
    $statsInitial = $this->service->getTalkStatistics($this->talk->id);
    
    expect(Redis::exists($redisKey))->toBe(1);
    
    $cachedData = json_decode(Redis::get($redisKey), true);
    expect($cachedData['average'])->toEqual(4.0);

    // Voto 2 (Este registro DEBE invalidar/actualizar la caché)
    $this->service->registerVote($this->talk->id, 2, hash('sha256', "device-b"));

    // Volvemos a pedir estadísticas
    $statsUpdated = $this->service->getTalkStatistics($this->talk->id);
    
    $cachedDataUpdated = json_decode(Redis::get($redisKey), true);
    expect($cachedDataUpdated['average'])->toEqual(3.0); // (4+2)/2 = 3.0
    expect($cachedDataUpdated['total_votes'])->toEqual(2);
});
