<?php

namespace ApiFrame\Http\Router;

use ApiFrame\Http\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class ContainerRequestHandlerResolver implements RequestHandlerResolver
{
    public function __construct(
        private ContainerInterface $container,
        private EndpointMap        $endpointSet,
    )
    {
    }

    public function getRequestHandlerFor(ServerRequestInterface $request): RequestHandlerInterface
    {
        $endpoint = $this->getEndpointFrom($request);
        $endpointMatch = $this->endpointSet->match($endpoint);

        if ($endpointMatch === null) {
            throw new NotFoundException('Route not found');
        }

        $handler = $this->container->get($endpointMatch->handler);

        if (!($handler instanceof RequestHandlerInterface)) {
            throw new RuntimeException('Invalid request handler type');
        }

        return $handler;
    }

    private function getEndpointFrom(ServerRequestInterface $request): Endpoint
    {
        return new Endpoint($request->getUri()->getPath(), $request->getMethod());
    }
}