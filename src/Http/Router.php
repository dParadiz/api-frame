<?php

namespace Api\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class Router implements RouterInterface
{
    private array $routeCollection;
    private ContainerInterface $container;

    public function __construct(array $routeCollection, ContainerInterface $container)
    {
        $this->routeCollection = $routeCollection;
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     * @throws Exception\NotFoundException
     */
    public function getRequestHandler(ServerRequestInterface $request): array
    {
        $path = $request->getUri()->getPath();

        if (isset($this->routeCollection['static'][$path])) {
            return [$this->prepareHandler($this->routeCollection['static'][$path]), $request];
        }

        foreach ($this->routeCollection['regex'] as $routeChunk) {
            if (!preg_match($routeChunk['regex'], $path, $matches)) {
                continue;
            }

            $pathData = $routeChunk['routeMap'][count($matches)];
            $vars = [];
            $i = 0;
            foreach ($pathData['variables'] ?? [] as $varName) {
                $vars[$varName] = $matches[++$i];
            }

            $request = $request->withAttribute('pathVars', $vars);

            return [$this->prepareHandler($pathData), $request];
        }

        throw new Exception\NotFoundException('Route not found');
    }

    /**
     * @param $pathData
     * @return MiddlewareHandler
     */
    private function prepareHandler(array $pathData): RequestHandlerInterface
    {
        $handler = new MiddlewareHandler($this->container->get($pathData['handler']));
        foreach ($pathData['middleware'] ?? [] as $middleware) {
            $handler->addMiddleware($this->container->get($middleware));
        }

        return $handler;
    }
}
