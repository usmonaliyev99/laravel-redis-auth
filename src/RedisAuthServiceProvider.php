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
        $this->mergeConfigFrom(__DIR__.'/../config/redis-auth.php', 'redis-auth');
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
