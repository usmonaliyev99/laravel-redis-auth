<?php

namespace Usmonaliyev\LaravelRedisAuth\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UnauthorizedException extends HttpResponseException
{
    /**
     * Create a new HTTP response exception instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function __construct()
    {
        $response = new Response(['message' => config('redis-auth.unauthorized_message')], Response::HTTP_UNAUTHORIZED);

        parent::__construct($response);
    }
}
