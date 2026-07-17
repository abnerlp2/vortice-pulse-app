<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Core\Evaluation\Services\StressSimulatorService;

class SimulateEvaluationStress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse:simulate-stress {talk_id} {--concurrent=300}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate massive concurrent evaluations for a specific talk';

    /**
     * Execute the console command.
     */
    public function handle(StressSimulatorService $simulator): int
    {
        $talkId = $this->argument('talk_id');
        $concurrent = (int) $this->option('concurrent');

        $this->info("Simulating {$concurrent} concurrent evaluations for talk {$talkId}...");

        $start = microtime(true);
        
        $simulator->simulate($talkId, $concurrent);

        $duration = round((microtime(true) - $start) * 1000);

        $this->info("Successfully injected {$concurrent} evaluations in {$duration} ms.");

        return 0;
    }
}
