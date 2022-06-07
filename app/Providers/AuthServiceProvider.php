<?php

namespace App\Providers;

use App\Passport\AccessTokenRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }
    }

    public function register()
    {
        parent::register();

        $this->app->bind(Bridge\AccessTokenRepository::class, function ($app) {
            return $app->make(AccessTokenRepository::class);
        });
    }
}
