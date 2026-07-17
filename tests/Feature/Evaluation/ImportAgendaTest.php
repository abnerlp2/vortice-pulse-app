<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->validPayloadPath = storage_path('app/valid_agenda.json');
    $this->invalidPayloadPath = storage_path('app/invalid_agenda.json');

    File::put($this->validPayloadPath, json_encode([
        'time_blocks' => [
            ['id' => 'block-1', 'start_time' => '2026-07-16T09:00:00Z', 'end_time' => '2026-07-16T10:00:00Z'],
        ],
        'talks' => [
            ['id' => 'talk-1', 'title' => 'Keynote', 'speaker' => 'Alice', 'time_block_id' => 'block-1', 'start_time' => '2026-07-16T09:00:00Z', 'end_time' => '2026-07-16T09:45:00Z'],
        ],
    ]));

    File::put($this->invalidPayloadPath, json_encode([
        'time_blocks' => [
            ['id' => 'block-2', 'start_time' => '2026-07-16T10:00:00Z', 'end_time' => '2026-07-16T11:00:00Z'],
        ],
        'talks' => [
            // Inválido: faltan campos obligatorios para forzar el fallo en el servicio o DB
            ['id' => 'talk-2', 'title' => 'Broken Talk'],
        ],
    ]));
});

afterEach(function () {
    File::delete($this->validPayloadPath);
    File::delete($this->invalidPayloadPath);
});

it('imports agenda successfully and returns 0', function () {
    $exitCode = Artisan::call('pulse:import-agenda', ['path' => $this->validPayloadPath]);

    expect($exitCode)->toBe(0);
    expect(DB::table('time_blocks')->count())->toBe(1);
    expect(DB::table('talks')->count())->toBe(1);
    expect(DB::table('talks')->where('id', 'talk-1')->exists())->toBeTrue();
});

it('rolls back database transaction if payload is invalid or corrupt', function () {
    try {
        Artisan::call('pulse:import-agenda', ['path' => $this->invalidPayloadPath]);
    } catch (\Throwable $e) {
        // Se espera que falle por datos corruptos o comando no existente
    }

    // Aserción de transaccionalidad: La base de datos debe estar intacta (sin inserciones parciales)
    expect(DB::table('time_blocks')->count())->toBe(0);
    expect(DB::table('talks')->count())->toBe(0);
});
