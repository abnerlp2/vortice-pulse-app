<?php

use App\Core\Evaluation\Services\EvaluationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    $this->validPayloadPath = Storage::path('valid_agenda.json');
    $this->invalidPayloadPath = Storage::path('invalid_agenda.json');

    Storage::put('valid_agenda.json', json_encode([
        'time_blocks' => [
            ['id' => 'block-1', 'start_time' => '2026-07-16T09:00:00Z', 'end_time' => '2026-07-16T10:00:00Z'],
        ],
        'talks' => [
            ['id' => 'talk-1', 'title' => 'Keynote', 'speaker' => 'Alice', 'time_block_id' => 'block-1', 'start_time' => '2026-07-16T09:00:00Z', 'end_time' => '2026-07-16T09:45:00Z'],
        ],
    ]));

    Storage::put('invalid_agenda.json', json_encode([
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
    // Storage::fake() cleans itself up, but we can keep these if we want. We'll just leave it empty.
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

it('imports csv using Spanish time headers', function () {
    $csv = implode("\n", [
        'id;titulo;conferencista;sala;time_block_id;Hora Inicio;Hora Fin;time_block_start_time;time_block_end_time',
        'talk-1;Keynote;Alice;Sala Principal;block-1;2026-08-12 09:00:00;2026-08-12 09:45:00;2026-08-12 09:00:00;2026-08-12 10:00:00',
    ]);

    $path = tempnam(sys_get_temp_dir(), 'agenda_csv_');
    file_put_contents($path, $csv);

    try {
        $service = new EvaluationService();

        expect($service->importAgendaFromCsv($path))->toBeTrue();

        expect(DB::table('time_blocks')->count())->toBe(1);
        expect(DB::table('talks')->count())->toBe(1);
        expect(DB::table('talks')->where('title', 'Keynote')->where('speaker', 'Alice')->exists())->toBeTrue();
        expect(DB::table('talks')->where('title', 'Keynote')->value('start_time'))->toBe('2026-08-12 09:00:00');
        expect(DB::table('talks')->where('title', 'Keynote')->value('end_time'))->toBe('2026-08-12 09:45:00');
    } finally {
        unlink($path);
    }
});
