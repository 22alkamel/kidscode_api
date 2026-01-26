<?php

namespace App\Providers;

use App\Models\ProgramGroup;
use App\Policies\ProgramGroupPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the policy mappings.
     */
    // protected $policies = [
    //     ProgramGroup::class => ProgramGroupPolicy::class,
    // ];
    protected $policies = [
    ProgramGroup::class => ProgramGroupPolicy::class,
];


    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate ability
        Gate::define('access', [ProgramGroupPolicy::class, 'access']);
    }
}
