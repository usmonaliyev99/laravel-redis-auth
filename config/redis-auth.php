<?php

return [

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

];
