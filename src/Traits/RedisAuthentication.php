<?php

namespace Usmonaliyev\LaravelRedisAuth\Traits;

use DateTimeInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Usmonaliyev\LaravelRedisAuth\Exceptions\NoAbilityException;

/**
 * trait RedisAuthentication
 */
trait RedisAuthentication
{
    /**
     *
     */
    public $abilities = [];

    /**
     * Create auth token to redis database.
     */
    public function createAuthToken(array $abilities = ['*'], ?DateTimeInterface $expiresAt = null)
    {
        $accessToken = bin2hex(openssl_random_pseudo_bytes(16));

        $signature = hash_hmac(config('redis-auth.algo'), $accessToken, config('redis-auth.secret_key'));

        $token = "$accessToken:$signature";

        $this->storeTokenToRedis($token, $abilities, $expiresAt);

        return $token;
    }

    /**
     * Store a token to redis database.
     */
    private function storeTokenToRedis(string $token, array $abilities, ?DateTimeInterface $expiresAt): void
    {
        $key = config('redis-auth.prefix') . $token;

        $this->abilities = collect($abilities)
            ->flip()
            ->toArray();

        Cache::put($key, $this, $expiresAt?->format('Y-m-d H:i:s'));
    }

    /**
     * Get a abilities property
     */
    public function getAbilities(): array
    {
        return $this->abilities;
    }

    public function check(mixed $ability, string $message = "You don't have ability..."): bool
    {
        if (!isset($this->abilities[$ability])) {
            throw new NoAbilityException(
                new JsonResponse([
                    'message' => $message
                ], JsonResponse::HTTP_FORBIDDEN)
            );
        }

        return true;
    }
}
