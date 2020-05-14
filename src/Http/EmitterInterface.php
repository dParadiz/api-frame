<?php

namespace Api\Http;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    public static function emit(ResponseInterface $response): void;
}
