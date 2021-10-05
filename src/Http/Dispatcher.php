<?php

namespace ApiFrame\Http;

use ApiFrame\Http\Router\Endpoint;
use ApiFrame\Http\Router\EndpointMap;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class Dispatcher
{
    public function __construct(
        private EndpointMap        $endpointSet,
        private ContainerInterface $handlerContainer,
        private EmitterInterface   $emitter
    )
    {
    }

    public function dispatch(ServerRequestInterface $request): void
    {
        $endpoint = $this->getEndpointFrom($request);
        $endpointMatch = $this->endpointSet->match($endpoint);

        if ($endpointMatch === null) {
            throw new Exception\NotFoundException('Route not found');
        }

        $handler = $this->getHandler($endpointMatch);

        $response = $handler->handle($request);

        $this->emitter->emit($response);
    }

    private function getEndpointFrom(ServerRequestInterface $request): Endpoint
    {
        return new Endpoint($request->getUri()->getPath(), $request->getMethod());
    }


    public function getHandler(Router\MapEntry $endpointMatch): RequestHandlerInterface
    {
        $handler = $this->handlerContainer->get($endpointMatch->handler);

        if (!($handler instanceof RequestHandlerInterface)) {
            throw new RuntimeException('Invalid request handler type');
        }
        return $handler;
    }
}
