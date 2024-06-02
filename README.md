# laravel-redis-auth

[![Latest Stable Version](http://poser.pugx.org/usmonaliyev/laravel-redis-auth/v)](https://packagist.org/packages/usmonaliyev/laravel-redis-auth)
[![Total Downloads](http://poser.pugx.org/usmonaliyev/laravel-redis-auth/downloads)](https://packagist.org/packages/usmonaliyev/laravel-redis-auth)
[![Latest Unstable Version](http://poser.pugx.org/usmonaliyev/laravel-redis-auth/v/unstable)](https://packagist.org/packages/usmonaliyev/laravel-redis-auth)
[![License](http://poser.pugx.org/usmonaliyev/laravel-redis-auth/license)](https://packagist.org/packages/usmonaliyev/laravel-redis-auth)
[![PHP Version Require](http://poser.pugx.org/usmonaliyev/laravel-redis-auth/require/php)](https://packagist.org/packages/usmonaliyev/laravel-redis-auth)


The Laravel Redis Auth Package is a Composer package that provides authentication functionality using a Redis database in Laravel applications. It offers a seamless integration with Laravel's authentication system while leveraging the speed and flexibility of Redis for storing user credentials.

## Purpose

I wanted to build microservices and I realized that I need a authentication service which can works with all microservices.
Then I had created this package. If you have three microservices, you should install this package to your all of them and you should connect services to one Redis database.
When client sends request to get new access token, one of your services creates access token and stores it to Redis database.
When client sends request to other service with acces token, that service can check token from Redis database.

## Requirements

- PHP >= 7.4
- predis/predis
- Redis database

## Installation

Install the package via Composer:

```bash
composer require usmonaliyev/laravel-redis-auth
```

## Publish

Publish the package configuration:

```bash
php artisan vendor:publish --provider="Usmonaliyev\LaravelRedisAuth\RedisAuthServiceProvider"
```

## Usage

Update the `.env` configuration to use the cache for authentication:

```bash
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

REDIS_PREFIX=''
```

You must add `Usmonaliyev\LaravelRedisAuth\Traits\RedisAuthentication` trait to you `App/Models/User` class.

```bash
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Usmonaliyev\LaravelRedisAuth\Traits\RedisAuthentication;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, RedisAuthentication;
    
    ...
```

After that you can create access token by `createAuthToken` function of `App/Models/User` class.
You can create token with abilities. The packge stored all tokens with abilities in Redis database.

```bash
$user = User::query()
    ->where('email', $request->string('email'))
    ->first();

$accessToken = $user->createAuthToken([
    'users.add',
    'users.show',
    'users.delete',
]);

```

You can check token's ability with `check` function of `App/Models/User` class in your Controllers.
If user does not have a ability, `check` function throw `Illuminate/Http/Exceptions/HttpResponseException`.

```bash
Auth::user()->check('users.add', "If you want to change error message!");

OR

auth()->user()->check('messages.show');
```

You can protect your routes by adding the auth middleware to them:

```bash
Route::middleware('redis-auth')->group(function () {
    // Protected routes
});
```

There are configurations in `redis-auth.php` in `config` folder.
You can change them.

```bash

    /**
     * Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..)
     * See hash_algos for a list of supported algorithms.
     */
    'algo' => env('REDIS_AUTH_ALGO', 'sha256'),

    /**
     * Secret key for creating a new token
     */
    'secret_key' => env('REDIS_AUTH_SECRET_KEY', 'laravel-redis-auth-secret-key'),

    /**
     * token_ttl represents the Token Time To Live, which defines the lifespan or expiration time of a token.
     */
    'token_ttl' => env('REDIS_AUTH_TOKEN_TTL', 3600 * 24),

    /**
     */
    'unauthorized_message' => env('UNAUTHORIZED_MESSAGE', 'Unauthorized...'),
```

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

## License

This package is open-source and released under the MIT License.

