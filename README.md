# Laravel Redis Auth Package

The Laravel Redis Auth Package is a Composer package that provides authentication functionality using a Redis database in Laravel applications. It offers a seamless integration with Laravel's authentication system while leveraging the speed and flexibility of Redis for storing user credentials.

## Features

- User registration with email and password
- User login and logout
- Password reset functionality
- Middleware for protecting routes based on authentication

## Requirements

- PHP >= 7.4
- Redis database

## Installation

Install the package via Composer:

```bash
composer require your-username/redis-auth-package
```

## Publish

Publish the package configuration:

```bash
php artisan vendor:publish --provider="Your\Namespace\RedisAuthPackageServiceProvider" --tag="config"
```

## Usage

Update the `.env` configuration to use the cache for authentication:

```bash
CACHE_DRIVER=redis

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

You can protect your routes by adding the auth middleware to them:

```bash
Route::group(['middleware' => 'auth'], function () {
    // Protected routes
});
```

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

## License

This package is open-source and released under the MIT License.

