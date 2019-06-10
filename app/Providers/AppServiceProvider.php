<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\QueryExecuted;
use Laravel\Lumen\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 注册mail
        $this->app->singleton('mailer', function (Application $app) {
            $app->configure('services');
            $app->configure('mail');

            return $app->loadComponent(
                'mail',
                'Illuminate\Mail\MailServiceProvider',
                'mailer'
            );
        });

        // 处理 dingo 的 findOrFail 问题
        // 或许可以放在 ApiExceptionServiceProvider 这样的地方
        app('api.exception')->register(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            abort(404);
        });

        // 开启日志
        if (app()->environment('local')) {
            app('db')->listen(function (QueryExecuted $query) {
                $sqlWithPlaceholders = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
                $bindings = $query->connection->prepareBindings($query->bindings);
                $pdo = $query->connection->getPdo();
                app('log')->info(vsprintf($sqlWithPlaceholders, array_map([$pdo, 'quote'], $bindings)));
            });
        }

        if (!app()->environment('production')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->configure('ide-helper');
        }
    }
}
