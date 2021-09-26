<?php

namespace Api\Http;

use Api\Http\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $handler = $this->router->getRequestHandler($request);

        $response = $handler->handle($request);

        $this->emitter->emit($response);
    }
}
