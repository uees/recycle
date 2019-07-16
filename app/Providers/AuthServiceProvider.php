<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            return auth_user();
        });

        Gate::before(function ($user, $ability) {
            // 管理员具有所有权限
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // 注册策略
        Gate::policy(\App\Models\Shipment::class, \App\Policies\ShipmentPolicy::class);
        Gate::policy(\App\Models\QcRecord::class, \App\Policies\QcRecordPolicy::class);
        Gate::policy(\App\Models\RecycledThing::class, \App\Policies\RecycledThingPolicy::class);
        Gate::policy(\App\Models\EnteringWarehouse::class, \App\Policies\EnteringWarehousePolicy::class);

        Gate::define('update-customers', function ($user, $customer) {
            return $user->hasRole(['finished_warehouse_keeper', 'boss', 'management_representative']);
        });

        Gate::define('update-roles', function ($user, $role) {
            return $user->hasRole(['boss', 'management_representative']);
        });
    }
}
