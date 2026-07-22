<?php

namespace App\Core\Evaluation\Repositories;

use App\Core\Evaluation\Contracts\EvaluationRepositoryInterface;
use App\Models\Evaluation;
use Illuminate\Support\Facades\DB;

class EvaluationRepository implements EvaluationRepositoryInterface
{
    public function clearAgenda(): void
    {
        // En un caso real masivo, DB::table()->delete() es mucho más eficiente que Eloquent para limpiarlo todo.
        // El orden importa para respetar las claves foráneas (borrar hijos primero).
        DB::table('evaluations')->delete();
        DB::table('talks')->delete();
        DB::table('time_blocks')->delete();
    }

    public function saveTimeBlocks(array $timeBlocks): void
    {
        $now = now();
        foreach ($timeBlocks as &$block) {
            $block['created_at'] = $now;
            $block['updated_at'] = $now;
        }

        DB::table('time_blocks')->insert($timeBlocks);
    }

    public function saveTalks(array $talks): void
    {
        $now = now();
        foreach ($talks as &$talk) {
            $talk['created_at'] = $now;
            $talk['updated_at'] = $now;
        }

        DB::table('talks')->insert($talks);
    }

    public function getTimeBlockById(string $id): ?object
    {
        return DB::table('time_blocks')->where('id', $id)->first();
    }

    public function getTalkById(string $id): ?object
    {
        return DB::table('talks')->where('id', $id)->first();
    }

    public function getTalkRatings(string $talkId): array
    {
        return DB::table('evaluations')
            ->where('talk_id', $talkId)
            ->pluck('rating')
            ->toArray();
    }

    public function getTalksByTimeBlock(string $timeBlockId): array
    {
        return DB::table('talks')
            ->where('time_block_id', $timeBlockId)
            ->pluck('id')
            ->toArray();
    }

    public function hasEvaluation(string $talkId, string $deviceSignature): bool
    {
        return DB::table('evaluations')
            ->where('talk_id', $talkId)
            ->where('device_signature', $deviceSignature)
            ->exists();
    }

    public function saveEvaluation(array $data): bool
    {
        $evaluation = new Evaluation($data);
        return $evaluation->save();
    }
}