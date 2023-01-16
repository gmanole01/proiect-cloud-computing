<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Auth\Guard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('custom', function($app, array $config) {
            return new UserProvider();
        });
        Auth::extend('custom', function($app, $name, array $config) {
            return new Guard(
                $app['tymon.jwt'],
                Auth::createUserProvider($config['provider']),
                $app->make('request'),
                $app['session.store'],
                $app->make('cookie')
            );
        });

        //
    }
}
