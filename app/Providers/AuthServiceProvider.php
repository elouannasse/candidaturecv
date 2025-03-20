<?php

namespace App\Providers;

use App\Models\Offre;
use App\Models\User;
use App\Policies\AuthPolicy;
use App\Policies\OffrePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Offre::class => OffrePolicy::class,
        // User::class => AuthPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
