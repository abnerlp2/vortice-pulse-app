<?php

namespace App\Core\Evaluation\Services;

use App\Core\Evaluation\Contracts\EvaluationRepositoryInterface;
use App\Models\RankingAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RankReconciliationService
{
    public function __construct(
        private EvaluationRepositoryInterface $repository,
        private RedisCacheHelper $cacheHelper
    ) {
    }

    /**
     * Process a batch of offline evaluations for a time block and return the alarm state.
     *
     * @param string $timeBlockId
     * @param array<int, array{talk_id:string,rating:int,device_signature:string,liked_aspects:?string,improvement_aspects:?string,created_at:string}> $pendingEvaluations
     * @return void
     */
    public function reconcile(string $timeBlockId, array $pendingEvaluations): void
    {
        $beforeOrder = $this->buildRankingOrder($timeBlockId);
        $this->processPendingEvaluations($timeBlockId, $pendingEvaluations);
        $afterOrder = $this->buildRankingOrder($timeBlockId);

        $affectedTalks = $this->findPermutationAffectedTalks($beforeOrder, $afterOrder);

        if (!empty($affectedTalks)) {
            RankingAlert::create([
                'time_block_id' => $timeBlockId,
                'affected_talks' => $affectedTalks,
                'details' => json_encode([
                    'before' => $beforeOrder,
                    'after' => $afterOrder,
                ]),
            ]);

            event(new \App\Core\Evaluation\Events\RankingOrderAltered(
                $timeBlockId,
                $beforeOrder,
                $afterOrder,
                $affectedTalks,
                'El orden consolidado del ranking cambió tras procesar propuestas offline.',
            ));
        }
    }

    private function buildRankingOrder(string $timeBlockId): array
    {
        $talkIds = $this->repository->getTalksByTimeBlock($timeBlockId);

        $scores = [];
        foreach ($talkIds as $talkId) {
            $ratings = $this->repository->getTalkRatings($talkId);
            $average = count($ratings) === 0 ? 0.0 : array_sum($ratings) / count($ratings);
            $scores[$talkId] = $average;
        }

        arsort($scores);

        return array_keys($scores);
    }

    private function processPendingEvaluations(string $timeBlockId, array $pendingEvaluations): void
    {
        $timeBlock = $this->repository->getTimeBlockById($timeBlockId);

        foreach ($pendingEvaluations as $evaluation) {
            if (!$this->isOfflineEvaluationValid($timeBlock, $evaluation)) {
                continue;
            }

            $cacheKey = "vortice:pulse:eval:{$evaluation['talk_id']}:{$evaluation['device_signature']}";

            if (!$this->cacheHelper->get($cacheKey) && !$this->repository->hasEvaluation($evaluation['talk_id'], $evaluation['device_signature'])) {
                $this->repository->saveEvaluation([
                    'talk_id' => $evaluation['talk_id'],
                    'rating' => $evaluation['rating'],
                    'device_signature' => $evaluation['device_signature'],
                    'liked_aspects' => $evaluation['liked_aspects'] ?? null,
                    'improvement_aspects' => $evaluation['improvement_aspects'] ?? null,
                ]);
                $this->cacheHelper->set($cacheKey, '1', 3600);
                $this->cacheHelper->delete("vortice:pulse:talk:{$evaluation['talk_id']}");
            }
        }
    }

    private function isOfflineEvaluationValid(?object $timeBlock, array $evaluation): bool
    {
        if (!$timeBlock || empty($evaluation['created_at'])) {
            return false;
        }

        try {
            $createdAt = Carbon::parse($evaluation['created_at']);
        } catch (\Throwable) {
            return false;
        }

        $startTime = Carbon::parse($timeBlock->start_time);
        $expirationTime = Carbon::parse($timeBlock->end_time)->addMinutes(10);

        return $createdAt->between($startTime, $expirationTime);
    }

    private function findPermutationAffectedTalks(array $before, array $after): array
    {
        $affected = [];
        $topBefore = array_slice($before, 0, 3);
        $topAfter = array_slice($after, 0, 3);

        foreach ($topAfter as $position => $talkId) {
            if (!isset($topBefore[$position]) || $topBefore[$position] !== $talkId) {
                $affected[] = $talkId;
            }
        }

        return array_values(array_unique($affected));
    }
}
