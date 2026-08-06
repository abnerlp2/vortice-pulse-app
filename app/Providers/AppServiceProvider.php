<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
/**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Core\Evaluation\Contracts\EvaluationRepositoryInterface::class,
            \App\Core\Evaluation\Repositories\EvaluationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
