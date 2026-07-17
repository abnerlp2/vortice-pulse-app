<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Core\Evaluation\Services\EvaluationService;
use Throwable;

class ImportAgenda extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse:import-agenda {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the JSON agenda (talks and time blocks) into the database';

    /**
     * Execute the console command.
     */
    public function handle(EvaluationService $service): int
    {
        $path = $this->argument('path');

        if (!File::exists($path)) {
            $this->error("El archivo JSON no fue encontrado en la ruta proporcionada: {$path}");
            return 1;
        }

        $jsonPayload = File::get($path);

        try {
            $service->importAgenda($jsonPayload);
            $this->info('Agenda importada exitosamente y persistida en base de datos.');
            return 0;
        } catch (Throwable $e) {
            $this->error('Error durante la importación (Transaction Rolled Back): ' . $e->getMessage());
            
            // Re-throw if running in a test environment to allow the test framework to assert the exact exception
            if (app()->runningUnitTests()) {
                throw $e;
            }
            
            return 1;
        }
    }
}
