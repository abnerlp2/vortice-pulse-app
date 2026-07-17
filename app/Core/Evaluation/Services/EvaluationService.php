<?php

namespace App\Core\Evaluation\Services;

use InvalidArgumentException;
use JsonException;
use App\Core\Evaluation\Contracts\EvaluationRepositoryInterface;
use App\Core\Evaluation\Exceptions\OutOfTimeBlockException;
use Illuminate\Support\Facades\DB;
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
                'start_time' => $startTime,
                'end_time' => $endTime,
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

            $talks[] = [
                'id' => $id,
                'title' => $title,
                'speaker' => $speaker,
                'time_block_id' => $timeBlockId,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        }

        return [
            'time_blocks' => array_values($timeBlocks),
            'talks' => $talks,
        ];
    }
}
