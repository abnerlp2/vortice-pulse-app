<?php

namespace App\Core\Evaluation\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StressSimulatorService
{
    /**
     * Simulates concurrent evaluations for a given talk.
     *
     * @param string $talkId
     * @param int $concurrent
     * @return void
     */
    public function simulate(string $talkId, int $concurrent): void
    {
        $evaluations = [];

        for ($i = 0; $i < $concurrent; $i++) {
            $evaluations[] = [
                'talk_id' => $talkId,
                'rating' => random_int(1, 5),
                'device_signature' => hash('sha256', Str::uuid()->toString() . $i),
                'liked_aspects' => null,
                'improvement_aspects' => null,
            ];
        }

        // Insert in chunks to simulate massive insert efficiently
        foreach (array_chunk($evaluations, 100) as $chunk) {
            DB::table('evaluations')->insert($chunk);
        }
    }
}
