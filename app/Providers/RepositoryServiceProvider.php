<?php

namespace App\Providers;

use App\Interfaces\OffreRepositoryInterface;
use App\Repositories\OffreRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(OffreRepositoryInterface::class, OffreRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
