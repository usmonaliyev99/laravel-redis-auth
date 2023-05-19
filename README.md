# Laravel Redis Auth Package

The Laravel Redis Auth Package is a Composer package that provides authentication functionality using a Redis database in Laravel applications. It offers a seamless integration with Laravel's authentication system while leveraging the speed and flexibility of Redis for storing user credentials.

## Features

- Middleware for protecting routes based on authentication

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

You can protect your routes by adding the auth middleware to them:

```bash
Route::group(['middleware' => 'redis-auth'], function () {
    // Protected routes
});
```

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

## License

This package is open-source and released under the MIT License.

