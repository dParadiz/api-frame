<?php

namespace Api\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareHandler implements RequestHandlerInterface
{
    /** @var MiddlewareInterface[] */
    private array $middleware = [];

    private RequestHandlerInterface $handler;

    public function __construct(RequestHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param MiddlewareInterface[] $middleware
     *
     * @return MiddlewareHandler
     */
    public function withMiddleware(array $middleware): MiddlewareHandler
    {
        $handler = clone $this;
        $handler->middleware = array_filter($middleware, fn($middleware) => $middleware instanceof MiddlewareInterface);

        return $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->middleware === []) {
            return $this->handler->handle($request);
        }

        $handler = array_reduce(
            $this->middleware,
            fn(RequestHandlerInterface $handler, MiddlewareInterface $middleware) => new MiddlewareAwareHandler($handler, $middleware),
            $this->handler
        );

        return $handler->handle($request);
    }
}

