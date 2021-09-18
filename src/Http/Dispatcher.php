<?php

namespace Api\Http;

use Api\Http\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class Dispatcher
{
    public function __construct(
        private RouterInterface  $router,
        private EmitterInterface $emitter
    )
    {
    }

    public function dispatch(ServerRequestInterface $request): void
    {
        [$handler, $request] = $this->router->getRequestHandler($request);

        if (!($handler instanceof RequestHandlerInterface)) {
            throw  new RuntimeException('Invalid handler type returned');
        }

        if (!($request instanceof ServerRequestInterface)) {
            throw  new RuntimeException('Invalid request type returned');
        }

        $response = $handler->handle($request);
        $this->emitter->emit($response);
    }
}
