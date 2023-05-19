<?php

namespace Usmonaliyev\LaravelRedisAuth;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Usmonaliyev\LaravelRedisAuth\Middleware\RedisAuthMiddleware;

class RedisAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/redis-auth.php', 'redis-auth');

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . "/../config/redis-auth.php" => config_path("redis-auth.php")
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('redis-auth', RedisAuthMiddleware::class);
    }
}
