<?php

namespace Usmonaliyev\LaravelRedisAuth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;
use Usmonaliyev\LaravelRedisAuth\Exceptions\UnauthorizedException;

/**
 * class RedisAuthMiddleware
 */
class RedisAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $receivedToken = $request->headers->get('authorization');

        if (strpos($receivedToken, 'Bearer ') === 0) {
            $receivedToken = substr($receivedToken, 7);
        }

        $token = $receivedToken;

        if (! strpos($token, ':')) {
            throw new UnauthorizedException();
        }

        [$receivedToken, $receivedSignature] = explode(':', $receivedToken);
        $calculatedSignature = hash_hmac(config('redis-auth.algo'), $receivedToken, config('redis-auth.secret_key'));

        $user = Redis::get($token);

        if ($receivedSignature !== $calculatedSignature or ! $user) {
            throw new UnauthorizedException();
        }

        Auth::login(unserialize($user));

        return $next($request);
    }
}
