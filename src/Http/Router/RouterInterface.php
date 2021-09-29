<?php

namespace ApiFrame\Http\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouterInterface
{
    public function getRequestHandler(ServerRequestInterface $request): RequestHandlerInterface;
}
