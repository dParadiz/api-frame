<?php

namespace Api\Http\Router;

use Api\Http\Exception;
use Api\Http\MiddlewareHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
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

    public function getRequestHandler(ServerRequestInterface $request): array
    {
        $path = $request->getUri()->getPath();

        if (isset($this->routeCollection->static[$path])) {
            return [$this->prepareHandler($this->routeCollection->static[$path]), $request];
        }

        foreach ($this->routeCollection->regex as $routeChunk) {
            if (!preg_match($routeChunk->regex, $path, $matches)) {
                continue;
            }

            $pathData = $routeChunk->routeMap[count($matches)];
            $vars = [];
            $i = 0;
            foreach ($pathData->variables as $varName) {
                $vars[$varName] = $matches[++$i];
            }

            $request = $request->withAttribute(self::PATH_VARS_ATTRIBUTE, $vars);

            return [$this->prepareHandler($pathData), $request];
        }

        throw new Exception\NotFoundException('Route not found');
    }

    private function prepareHandler(PathData $pathData): RequestHandlerInterface
    {
        $handler = $this->container->get($pathData->handler);

        if (!($handler instanceof RequestHandlerInterface)) {
            throw new RuntimeException('Invalid request handler type');
        }

        $handlerMiddlewareDecorator = new MiddlewareHandler($handler);

        $middleware = array_map([$this, 'loadMiddleware'], $pathData->middleware);

        return $handlerMiddlewareDecorator->withMiddleware($middleware);
    }

    private function loadMiddleware(string $className): MiddlewareInterface
    {
        $middleware = $this->container->get($className);

        if (!($middleware instanceof MiddlewareInterface)) {
            throw  new RuntimeException('Invalid middleware type');
        }

        return $middleware;
    }
}
