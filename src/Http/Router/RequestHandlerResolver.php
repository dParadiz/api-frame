<?php

namespace ApiFrame\Http\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RequestHandlerResolver
{
    public function getRequestHandlerFor(ServerRequestInterface $request): RequestHandlerInterface;
}