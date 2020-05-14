<?php

namespace Api\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareAwareHandler implements RequestHandlerInterface
{
    private RequestHandlerInterface $handler;

    private MiddlewareInterface $middleware;

    public function __construct(RequestHandlerInterface $handler, MiddlewareInterface $middleware)
    {
        $this->handler = $handler;
        $this->middleware = $middleware;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middleware->process($request, $this->handler);
    }
}
