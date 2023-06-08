<?php

namespace Usmonaliyev\LaravelRedisAuth\Contracts;

use DateTime;
use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Redis;
use Usmonaliyev\LaravelRedisAuth\Exceptions\NoAbilityException;

class RedisAuthenticatable extends User
{
  /**
   * User's abilities
   *
   * @var array
   */
  public $abilities = [];

  /**
   * Create auth token to redis database.
   *
   * @param array $abilities
   * @param DateTimeInterface|null $expiresAt
   *
   * @return stirng
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
   * @param DateTimeInterface|null $expiresAt
   *
   * @return void
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
   * @param stirng $message
   *
   * @return void
   */
  public function hasAbility(mixed $ability, string $message = "You don't have ability..."): void
  {
    if (!isset($this->abilities[$ability])) {
      throw new NoAbilityException(
        new JsonResponse([
          'message' => $message
        ], JsonResponse::HTTP_FORBIDDEN)
      );
    }
  }
}
