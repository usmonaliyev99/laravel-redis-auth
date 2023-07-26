<?php

namespace Usmonaliyev\LaravelRedisAuth\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class NoAbilityException extends HttpResponseException
{
  public function __construct(string $message, int $status = Response::HTTP_FORBIDDEN)
  {
    parent::__construct(response()->json(['message' => $message], $status));
  }
}
