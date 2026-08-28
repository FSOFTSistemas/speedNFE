<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\PreVenda;
use App\Policies\PreVendaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PreVenda::class => PreVendaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('menu-faturas', function ($user) {
            return $user && strtolower($user->tipo ?? '') === 'admin';
        });

        Gate::define('menu-administracao', function ($user) {
            return $user && ($user->cargo === 'master' || strtolower($user->tipo ?? '') === 'admin');
        });
    }
}
