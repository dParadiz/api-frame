<?php

namespace Api\Http\Router;

use Api\Http\Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class Router implements RouterInterface
{
    public const PATH_VARS_ATTRIBUTE = 'pathVars';

    public function __construct(
        private RouteCollection    $routeCollection,
        private ContainerInterface $container)
    {
    }

    public function getRequestHandler(ServerRequestInterface $request): RequestHandlerInterface
    {
        $path = $request->getUri()->getPath();

        $pathData = $this->routeCollection->match($path);

        if ($pathData === null) {
            throw new Exception\NotFoundException('Route not found');
        }

        if ($pathData->method !== '' && $pathData->method !== strtoupper($request->getMethod())) {
            throw new Exception\MethodNotAllowed();
        }

        return $this->prepareHandler($pathData);
    }

    private function prepareHandler(PathData $pathData): RequestHandlerInterface
    {
        $handler = $this->container->get($pathData->handler);

        if (!($handler instanceof RequestHandlerInterface)) {
            throw new RuntimeException('Invalid request handler type');
        }

        return $handler;
    }


}
