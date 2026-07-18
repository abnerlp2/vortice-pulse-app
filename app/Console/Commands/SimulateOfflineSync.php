<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Core\Evaluation\Services\RankReconciliationService;
use App\Core\Evaluation\Services\EvaluationService;
use App\Core\Evaluation\Repositories\EvaluationRepository;
use Illuminate\Support\Str;

class SimulateOfflineSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse:simulate-offline-sync {time_block_id} {--batch=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate offline pending evaluations being processed for a time block';

    public function handle(): int
    {
        $timeBlockId = $this->argument('time_block_id');
        $batch = (int) $this->option('batch');

        $this->info("Simulating offline sync for time block {$timeBlockId} with batch size {$batch}...");

        $pendingEvaluations = [];
        for ($i = 0; $i < $batch; $i++) {
            $pendingEvaluations[] = [
                'talk_id' => 'talk-active',
                'rating' => random_int(1, 5),
                'device_signature' => hash('sha256', Str::uuid()->toString() . $i),
                'liked_aspects' => null,
                'improvement_aspects' => null,
                'created_at' => now()->toDateTimeString(),
            ];
        }

        $startTime = microtime(true);
        app(RankReconciliationService::class)->reconcile($timeBlockId, $pendingEvaluations);
        $duration = (microtime(true) - $startTime) * 1000;

        $this->info(sprintf('Offline sync simulation completed. Processed %d evaluations in %.2f ms.', $batch, $duration));

        return 0;
    }
}
