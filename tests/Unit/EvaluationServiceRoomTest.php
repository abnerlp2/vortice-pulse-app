<?php

use App\Core\Evaluation\Services\EvaluationService;
use App\Models\Talk;
use App\Models\TimeBlock;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = new EvaluationService();
});

it('imports csv with flexible title speaker and room headers and preserves nullable room values', function (): void {
    $csv = implode("\n", [
        'id;titulo;conferencista;sala;time_block_id;start_time;end_time;time_block_start_time;time_block_end_time',
        'talk-1;Primera Charla;Ana López;Sala Principal;block-1;2026-08-12 09:00:00;2026-08-12 09:30:00;2026-08-12 09:00:00;2026-08-12 10:00:00',
        'talk-2;Second Talk;John Doe;;block-1;2026-08-12 09:30:00;2026-08-12 10:00:00;2026-08-12 09:00:00;2026-08-12 10:00:00',
    ]);

    $path = tempnam(sys_get_temp_dir(), 'agenda_csv_');
    file_put_contents($path, $csv);

    try {
        expect($this->service->importAgendaFromCsv($path))->toBeTrue();

        expect(TimeBlock::count())->toBe(1);
        expect(Talk::count())->toBe(2);

        $talk1 = Talk::where('title', 'Primera Charla')->first();
        $talk2 = Talk::where('title', 'Second Talk')->first();

        expect($talk1)->not->toBeNull();
        expect($talk1->speaker)->toBe('Ana López');
        expect($talk1->room)->toBe('Sala Principal');
        expect($talk1->time_block_id)->toBe('block-1');

        expect($talk2)->not->toBeNull();
        expect($talk2->speaker)->toBe('John Doe');
        expect($talk2->room)->toBeNull();
        expect($talk2->time_block_id)->toBe('block-1');
    } finally {
        unlink($path);
    }
});
