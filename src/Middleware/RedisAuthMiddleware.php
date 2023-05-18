<?php

namespace Usmonaliyev\LaravelRedisAuth\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

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
        $key = config('redis-auth.prefix') . $token;

        [$receivedToken, $receivedSignature] = explode(':', $receivedToken);
        $calculatedSignature = hash_hmac(config('redis-auth.algo'), $receivedToken, config('redis-auth.secret_key'));

        $user = Cache::get($key);

        if ($receivedSignature !== $calculatedSignature or !$user) {
            throw new HttpResponseException(
                response: new JsonResponse(data: [
                    'message' => 'Unauthorized...',
                ], status: HttpResponse::HTTP_UNAUTHORIZED)
            );
        }

        Auth::login($user);

        return $next($request);
    }
}
