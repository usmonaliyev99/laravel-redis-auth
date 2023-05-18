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

];
