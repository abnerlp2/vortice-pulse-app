<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\Evaluation\Contracts\EvaluationRepositoryInterface;
use App\Repositories\EloquentEvaluationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EvaluationRepositoryInterface::class, EloquentEvaluationRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
