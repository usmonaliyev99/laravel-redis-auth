<?php

namespace Usmonaliyev\LaravelRedisAuth\Traits;

use DateTime;
use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
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
        $this->abilities = collect($abilities)
            ->flip()
            ->toArray();

        if ($expiresAt) {
            $diff = date_diff(new DateTime(), $expiresAt);

            $seconds = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->days * 86400);

            Redis::setex($token, $seconds, serialize($this));
        } else {
            Redis::setex($token, config('redis-auth.token_ttl'), serialize($this));
        }
    }

    /**
     * Get a abilities property
     */
    public function getAbilities(): array
    {
        return $this->abilities;
    }

    /**
     * Check a ability
     */
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
