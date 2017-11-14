<?php

namespace App\Providers;

use App\Guards\ApiGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('api', function ($app, array $config) {
            return new ApiUserProvider();
        });

        Auth::extend('apiguard', function ($app, $name, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\Guard...
            return new ApiGuard(Auth::createUserProvider($config['provider']), [
                'username' => request()->header()['php-auth-user'][0],
                'password' => request()->header()['php-auth-pw'][0]
            ]);
        });
    }
}
