<?php

namespace Usmonaliyev\LaravelRedisAuth\Traits;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;
use Usmonaliyev\LaravelRedisAuth\Exceptions\NoAbilityException;

/**
 * trait RedisAuthentication
 */
trait RedisAuthentication
{
    public $abilities = [];

    /**
     * Create auth token to redis database.
     *
     * @param array $abilities
     * @param ?DateTimeInterface $expiresAt
     *
     * @return string
     */
    public function createAuthToken(array $abilities = ['*'], ?DateTimeInterface $expiresAt = null): string
    {
        $accessToken = bin2hex(openssl_random_pseudo_bytes(16));

        $signature = hash_hmac(config('redis-auth.algo'), $accessToken, config('redis-auth.secret_key'));

        $token = "$accessToken:$signature";

        $this->storeTokenToRedis($token, $abilities, $expiresAt);

        return $token;
    }

    /**
     * Store a token to redis database.
     *
     * @param string $token
     * @param array $abilities
     * @param ?DateTimeInterface $expiresAt
     */
    private function storeTokenToRedis(string $token, array $abilities, ?DateTimeInterface $expiresAt): void
    {
        $this->abilities = $abilities;

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
     *
     * @return array
     */
    public function getAbilities(): array
    {
        return $this->abilities;
    }

    /**
     * Check a ability
     *
     * @param mixed $ability
     * @param string $message 
     *
     * @throws NoAbilityException
     * @return bool
     */
    public function check(mixed $ability, string $message = "You don't have ability...", bool $each = true): bool
    {
        $error = new NoAbilityException($message);

        if (is_string($ability)) {

            if (!in_array($ability, $this->abilities)) {
                throw $error;
            };

            return true;
        }

        if (is_array($ability)) {

            $existAbilities = array_filter($ability, fn ($a) => in_array($a, $this->abilities));

            if ($each) {
                if (count($existAbilities) != count($ability)) throw $error;
            } else {
                if (empty($existAbilities)) throw $error;
            }

            return true;
        }

        throw new InvalidArgumentException("The ability must be either a string or an array.");
    }

    /**
     * Check a ability
     *
     * @param mixed $ability
     * @param string $message 
     *
     * @throws NoAbilityException
     * @return bool
     */
    public function hasAbility(mixed $ability, bool $each = true): bool
    {
        if (is_string($ability)) {

            if (!in_array($ability, $this->abilities)) {
                return false;
            };

            return true;
        }

        if (is_array($ability)) {

            $existAbilities = array_filter($ability, fn ($a) => in_array($a, $this->abilities));

            if ($each) {
                if (count($existAbilities) != count($ability)) return false;
            } else {
                return false;
            }

            return true;
        }

        throw new InvalidArgumentException("The ability must be either a string or an array.");
    }
}
