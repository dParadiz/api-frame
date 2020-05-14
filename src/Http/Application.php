<?php
namespace Api\Http;

use Psr\Http\Message\ServerRequestInterface;

class Application
{
    private RouterInterface $router;
    private EmitterInterface $emitter;

    public function __construct(RouterInterface $router, EmitterInterface $emitter)
    {
        $this->router = $router;
        $this->emitter = $emitter;
    }

    public function dispatch(ServerRequestInterface $request): void
    {
        [$handler, $request] = $this->router->getRequestHandler($request);
        $response = $handler->handle($request);
        $this->emitter->emit($response);
    }
}
