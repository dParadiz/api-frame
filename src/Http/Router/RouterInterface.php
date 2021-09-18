<?php

namespace Api\Http\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouterInterface
{
    /**
     * @return RequestHandlerInterface[]|ServerRequestInterface[]
     */
    public function getRequestHandler(ServerRequestInterface $request): array;
}
