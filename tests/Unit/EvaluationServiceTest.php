<?php

use App\Core\Evaluation\Services\EvaluationService;
use InvalidArgumentException;

it('parses a valid JSON payload into agenda data', function (): void {
    $service = new EvaluationService();

    $payload = json_encode([
        'time_blocks' => [
            [
                'id' => 'block-1',
                'start_time' => '2026-07-16T09:00:00Z',
                'end_time' => '2026-07-16T10:00:00Z',
            ],
        ],
        'talks' => [
            [
                'id' => 'talk-1',
                'title' => 'Opening Keynote',
                'speaker' => 'Ana López',
                'time_block_id' => 'block-1',
                'start_time' => '2026-07-16T09:00:00Z',
                'end_time' => '2026-07-16T09:45:00Z',
            ],
        ],
    ], JSON_THROW_ON_ERROR);

    $result = $service->parsePayload($payload);

    expect($result)
        ->toBeArray()
        ->and($result['time_blocks'])->toHaveCount(1)
        ->and($result['talks'])->toHaveCount(1)
        ->and($result['talks'][0]['title'])->toBe('Opening Keynote');
});

it('rejects invalid JSON payloads and malformed agenda rules', function (): void {
    $service = new EvaluationService();

    expect(fn () => $service->parsePayload('{invalid json}'))->toThrow(InvalidArgumentException::class);

    $invalidPayload = json_encode([
        'time_blocks' => [
            [
                'id' => 'block-1',
                'start_time' => '2026-07-16T10:00:00Z',
                'end_time' => '2026-07-16T09:00:00Z',
            ],
        ],
        'talks' => [
            [
                'id' => 'talk-1',
                'title' => 'Broken Talk',
                'speaker' => 'Ana López',
                'time_block_id' => 'block-1',
                'start_time' => '2026-07-16T10:00:00Z',
                'end_time' => '2026-07-16T09:00:00Z',
            ],
        ],
    ], JSON_THROW_ON_ERROR);

    expect(fn () => $service->parsePayload($invalidPayload))->toThrow(InvalidArgumentException::class);
});
