<?php

namespace App\Core\Evaluation\Services;

use InvalidArgumentException;
use JsonException;
use App\Core\Evaluation\Contracts\EvaluationRepositoryInterface;
use App\Core\Evaluation\Exceptions\OutOfTimeBlockException;
use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EvaluationService
{
    public function __construct(
        private ?EvaluationRepositoryInterface $repository = null,
        private ?RedisCacheHelper $cacheHelper = null
    ) {}

    /**
     * Import the agenda from a JSON string into the persistence layer inside a database transaction.
     *
     * @param string $payload
     * @return void
     */
    public function importAgenda(string $payload): void
    {
        $parsed = $this->parsePayload($payload);

        $repository = $this->repository ?? app(EvaluationRepositoryInterface::class);

        DB::transaction(function () use ($parsed, $repository) {
            $repository->clearAgenda();
            $repository->saveTimeBlocks($parsed['time_blocks']);
            $repository->saveTalks($parsed['talks']);
        });
    }

    /**
     * Import agenda data from a CSV file and persist it into the database.
     *
     * @param string $filePath
     * @return bool
     */
    public function importAgendaFromCsv(string $filePath): bool
    {
        if (!is_readable($filePath)) {
            throw new InvalidArgumentException("CSV file not found or not readable: {$filePath}");
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new InvalidArgumentException("CSV file not found or not readable: {$filePath}");
        }

        $content = trim(str_replace(["\r\n", "\r"], "\n", $content));
        $lines = array_filter(array_map('trim', explode("\n", $content)), fn ($line) => $line !== '');

        if (empty($lines)) {
            throw new InvalidArgumentException('The CSV file does not contain any valid rows.');
        }

        $headerLine = array_shift($lines);
        $delimiter = strpos($headerLine, ';') !== false ? ';' : ',';
        [$headers, $rows] = $this->parseCsvRows($headerLine, $lines, $delimiter);

        if (empty($rows)) {
            [$headers, $rows] = $this->parseCsvRows($headerLine, $lines, $delimiter === ';' ? ',' : ';');
        }

        if (empty($rows)) {
            throw new InvalidArgumentException('The CSV file does not contain any valid rows.');
        }

        $repository = $this->repository ?? app(EvaluationRepositoryInterface::class);

        DB::transaction(function () use ($rows, $repository) {
            $repository->clearAgenda();

            foreach ($rows as $index => $row) {
                $title = trim((string) $this->getCsvValue($row, ['title', 'título', 'titulo']));
                $speaker = trim((string) $this->getCsvValue($row, ['speaker', 'conferencista']));
                $room = $this->getCsvValue($row, ['room', 'sala', 'auditorio', 'ubicacion'], false);
                $timeBlockId = trim((string) ($row['time_block_id'] ?? $row['time_block'] ?? $row['block'] ?? ''));
                $talkStart = trim((string) $this->getCsvValue($row, ['hora inicio', 'start_time', 'start time', 'hora_inicio']));
                $talkEnd = trim((string) $this->getCsvValue($row, ['hora fin', 'end_time', 'end time', 'hora_fin']));
                $blockStart = trim((string) ($row['time_block_start_time'] ?? $row['block_start_time'] ?? $row['time_block_start'] ?? ''));
                $blockEnd = trim((string) ($row['time_block_end_time'] ?? $row['block_end_time'] ?? $row['time_block_end'] ?? ''));

                if ($title === '' || $speaker === '' || $timeBlockId === '' || $talkStart === '' || $talkEnd === '') {
                    throw new InvalidArgumentException(sprintf('CSV row %d is missing required talk fields.', $index + 1));
                }

                try {
                    $talkStartDateTime = \Carbon\Carbon::parse($talkStart);
                    $talkEndDateTime = \Carbon\Carbon::parse($talkEnd);
                } catch (\Throwable $e) {
                    throw new InvalidArgumentException(sprintf('CSV row %d contains an invalid talk time range.', $index + 1), 0, $e);
                }

                if ($talkEndDateTime->lte($talkStartDateTime)) {
                    throw new InvalidArgumentException(sprintf('CSV row %d has an invalid talk time range.', $index + 1));
                }

                $blockStartValue = $blockStart !== '' ? $blockStart : $talkStart;
                $blockEndValue = $blockEnd !== '' ? $blockEnd : $talkEnd;

                try {
                    $blockStartDateTime = \Carbon\Carbon::parse($blockStartValue);
                    $blockEndDateTime = \Carbon\Carbon::parse($blockEndValue);
                } catch (\Throwable $e) {
                    throw new InvalidArgumentException(sprintf('CSV row %d contains an invalid block time range.', $index + 1), 0, $e);
                }

                if ($blockEndDateTime->lte($blockStartDateTime)) {
                    throw new InvalidArgumentException(sprintf('CSV row %d has an invalid block time range.', $index + 1));
                }

                if ($talkStartDateTime->lt($blockStartDateTime) || $talkEndDateTime->gt($blockEndDateTime)) {
                    throw new InvalidArgumentException(sprintf('CSV row %d has a talk outside its time block.', $index + 1));
                }

                $timeBlock = TimeBlock::firstOrCreate(
                    ['id' => $timeBlockId],
                    [
                        'start_time' => $blockStartDateTime->format('Y-m-d H:i:s'),
                        'end_time' => $blockEndDateTime->format('Y-m-d H:i:s'),
                    ]
                );

                $talkUuid = (string) Str::uuid();

                Talk::create([
                    'id' => $talkUuid,
                    'title' => $title,
                    'speaker' => $speaker,
                    'room' => $room,
                    'time_block_id' => $timeBlock->id,
                    'start_time' => $talkStartDateTime->format('Y-m-d H:i:s'),
                    'end_time' => $talkEndDateTime->format('Y-m-d H:i:s'),
                ]);

                Cache::forget("vortice:pulse:talk:{$talkUuid}");
            }
        });

        Cache::forget('vortice:time_blocks:active');
        Cache::forget('vortice:talks:all');

        return true;
    }

    private function parseCsvRows(string $headerLine, array $lines, string $delimiter): array
    {
        $rawHeader = str_getcsv($headerLine, $delimiter);
        $headers = array_map([$this, 'normalizeCsvHeader'], $rawHeader);
        $rows = [];

        foreach ($lines as $line) {
            $row = array_map('trim', str_getcsv($line, $delimiter));

            if (count($row) !== count($headers)) {
                continue;
            }

            $rows[] = array_combine($headers, $row);
        }

        return [$headers, $rows];
    }

    /**
     * Normalize CSV headers by removing UTF-8 BOM and converting values to canonical snake_case.
     *
     * @param string|null $header
     * @return string
     */
    private function normalizeCsvHeader(?string $header): string
    {
        $value = (string) $header;
        $value = trim(ltrim($value, "\xEF\xBB\xBF"));
        $value = mb_strtolower($value);
        $value = str_replace([' ', '-', '.', '/'], '_', $value);

        return trim($value, '_');
    }

    /**
     * Retrieve a CSV row value by matching against a list of possible header names.
     *
     * @param array<string, mixed> $row
     * @param array<int, string> $candidates
     * @param bool $required
     * @return string|null
     */
    private function getCsvValue(array $row, array $candidates, bool $required = true): ?string
    {
        foreach ($candidates as $candidate) {
            $normalizedCandidate = $this->normalizeCsvHeader($candidate);

            foreach ([$candidate, $normalizedCandidate] as $key) {
                if (array_key_exists($key, $row) && $row[$key] !== null) {
                    $value = trim((string) $row[$key]);

                    if ($value === '' && !$required) {
                        return null;
                    }

                    return $value;
                }
            }
        }

        if ($required) {
            return '';
        }

        return null;
    }

    /**
     * Registra un voto para una charla específica validando reglas de negocio y previniendo duplicados.
     *
     * @param string $talkId
     * @param int $rating
     * @param string $deviceSignature
     * @param string|null $likedAspects
     * @param string|null $improvementAspects
     * @return void
     * @throws ValidationException
     * @throws OutOfTimeBlockException
     */
    public function registerVote(string $talkId, int $rating, string $deviceSignature, ?string $likedAspects = null, ?string $improvementAspects = null): void
    {
        $repository = $this->repository ?? app(EvaluationRepositoryInterface::class);
        $cacheHelper = $this->cacheHelper ?? app(RedisCacheHelper::class);

        $talk = $repository->getTalkById($talkId);

        if (!$talk) {
            throw ValidationException::withMessages(['talkId' => 'La charla no existe.']);
        }

        $timeBlock = $repository->getTimeBlockById($talk->time_block_id);

        if (!$timeBlock) {
            throw ValidationException::withMessages(['talkId' => 'Bloque de tiempo de la charla no encontrado.']);
        }

        $now = now();
        $startTime = \Carbon\Carbon::parse($timeBlock->start_time);
        $endTimeWithTolerance = \Carbon\Carbon::parse($timeBlock->end_time)->addMinutes(30);

        if ($now->isBefore($startTime) || $now->isAfter($endTimeWithTolerance)) {
            throw new OutOfTimeBlockException("Las evaluaciones solo están permitidas durante la charla y hasta 30 minutos después de finalizado el bloque.");
        }

        // Validar límite numérico
        if ($rating < 1 || $rating > 5) {
            throw ValidationException::withMessages(['rating' => 'El rating debe estar entre 1 y 5 corazones.']);
        }

        // Prevenir duplicidad (Regla principal contra colisión SQL por unique constraints)
        if ($repository->hasEvaluation($talkId, $deviceSignature)) {
            throw ValidationException::withMessages([
                'deviceSignature' => 'Ya has emitido una valoración para esta charla.'
            ]);
        }

        $repository->saveEvaluation([
            'talk_id' => $talkId,
            'rating' => $rating,
            'device_signature' => $deviceSignature,
            'liked_aspects' => $likedAspects,
            'improvement_aspects' => $improvementAspects,
        ]);

        // Invalidar caché tras registrar exitosamente el voto
        $cacheHelper->delete("vortice:pulse:talk:{$talkId}");

        $statistics = $this->getTalkStatistics($talkId);
        event(new \App\Core\Evaluation\Events\EvaluationReceived(
            $talkId,
            $talk->time_block_id,
            $statistics['average'],
            $statistics['total_votes'],
            $rating,
        ));
    }

    /**
     * Calcula las métricas estadísticas de la charla e implementa el pipeline de Redis.
     *
     * @param string $talkId
     * @return array{average: float, standard_deviation: float, total_votes: int}
     */
    public function getTalkStatistics(string $talkId): array
    {
        $cacheHelper = $this->cacheHelper ?? app(RedisCacheHelper::class);
        $cacheKey = "vortice:pulse:talk:{$talkId}";

        $cachedData = $cacheHelper->get($cacheKey);
        
        if ($cachedData !== null) {
            return json_decode($cachedData, true);
        }

        $repository = $this->repository ?? app(EvaluationRepositoryInterface::class);
        $ratings = $repository->getTalkRatings($talkId);

        $totalVotes = count($ratings);

        if ($totalVotes === 0) {
            $stats = [
                'average' => 0.0,
                'standard_deviation' => 0.0,
                'total_votes' => 0
            ];
        } elseif ($totalVotes < 2) {
            // Protección matemática: Para 1 elemento, la desviación estándar es 0 y se evita división por cero en fórmulas muestrales.
            $stats = [
                'average' => (float) $ratings[0],
                'standard_deviation' => 0.0,
                'total_votes' => 1
            ];
        } else {
            $sum = array_sum($ratings);
            $average = $sum / $totalVotes;

            $varianceSum = 0.0;
            foreach ($ratings as $rating) {
                $varianceSum += pow($rating - $average, 2);
            }

            // Desviación Estándar Poblacional pura
            $variance = $varianceSum / $totalVotes;
            $standardDeviation = sqrt($variance);

            $stats = [
                'average' => round($average, 4),
                'standard_deviation' => round($standardDeviation, 4),
                'total_votes' => $totalVotes
            ];
        }

        // Cachear resultado (ej. expiración de 10 minutos)
        $cacheHelper->set($cacheKey, json_encode($stats), 600);

        return $stats;
    }

    /**
     * @return array{time_blocks: array<int, array{id:string,start_time:string,end_time:string}>, talks: array<int, array{id:string,title:string,speaker:string,time_block_id:string,start_time:string,end_time:string}>}
     */
    public function parsePayload(string $payload): array
    {
        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Invalid JSON payload.', 0, $exception);
        }

        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Payload must decode to an array.');
        }

        if (!isset($decoded['time_blocks']) || !is_array($decoded['time_blocks'])) {
            throw new InvalidArgumentException('Payload must include time_blocks.');
        }

        if (!isset($decoded['talks']) || !is_array($decoded['talks'])) {
            throw new InvalidArgumentException('Payload must include talks.');
        }

        $timeBlocks = [];
        foreach ($decoded['time_blocks'] as $index => $block) {
            if (!is_array($block)) {
                throw new InvalidArgumentException(sprintf('time_blocks[%d] must be an object.', $index));
            }

            $id = $block['id'] ?? null;
            $startTime = $block['start_time'] ?? null;
            $endTime = $block['end_time'] ?? null;

            if (!is_string($id) || $id === '' || !is_string($startTime) || !is_string($endTime)) {
                throw new InvalidArgumentException(sprintf('time_blocks[%d] is invalid.', $index));
            }

            $startTimestamp = strtotime($startTime);
            $endTimestamp = strtotime($endTime);

            if ($startTimestamp === false || $endTimestamp === false || $endTimestamp <= $startTimestamp) {
                throw new InvalidArgumentException(sprintf('time_blocks[%d] has an invalid time range.', $index));
            }

            $timeBlocks[$id] = [
                'id' => $id,
                'start_time' => date('Y-m-d H:i:s', $startTimestamp),
                'end_time' => date('Y-m-d H:i:s', $endTimestamp),
            ];
        }

        $talks = [];
        foreach ($decoded['talks'] as $index => $talk) {
            if (!is_array($talk)) {
                throw new InvalidArgumentException(sprintf('talks[%d] must be an object.', $index));
            }

            $id = $talk['id'] ?? null;
            $title = $talk['title'] ?? null;
            $speaker = $talk['speaker'] ?? null;
            $timeBlockId = $talk['time_block_id'] ?? null;
            $startTime = $talk['start_time'] ?? null;
            $endTime = $talk['end_time'] ?? null;

            if (!is_string($id) || $id === '' || !is_string($title) || !is_string($speaker) || !is_string($timeBlockId) || !is_string($startTime) || !is_string($endTime)) {
                throw new InvalidArgumentException(sprintf('talks[%d] is invalid.', $index));
            }

            if (!isset($timeBlocks[$timeBlockId])) {
                throw new InvalidArgumentException(sprintf('talks[%d] references an unknown time block.', $index));
            }

            $startTimestamp = strtotime($startTime);
            $endTimestamp = strtotime($endTime);

            if ($startTimestamp === false || $endTimestamp === false || $endTimestamp <= $startTimestamp) {
                throw new InvalidArgumentException(sprintf('talks[%d] has an invalid time range.', $index));
            }

            $blockStart = strtotime($timeBlocks[$timeBlockId]['start_time']);
            $blockEnd = strtotime($timeBlocks[$timeBlockId]['end_time']);

            if ($startTimestamp < $blockStart || $endTimestamp > $blockEnd) {
                throw new InvalidArgumentException(sprintf('talks[%d] falls outside its time block.', $index));
            }

            $room = isset($talk['room']) && is_string($talk['room']) ? $talk['room'] : null;

            $talks[] = [
                'id' => $id,
                'title' => $title,
                'speaker' => $speaker,
                'room' => $room,
                'time_block_id' => $timeBlockId,
                'start_time' => date('Y-m-d H:i:s', $startTimestamp),
                'end_time' => date('Y-m-d H:i:s', $endTimestamp),
            ];
        }

        return [
            'time_blocks' => array_values($timeBlocks),
            'talks' => $talks,
        ];
    }
}
