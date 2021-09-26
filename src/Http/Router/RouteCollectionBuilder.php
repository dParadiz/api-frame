<?php

namespace Api\Http\Router;

use Psr\Http\Server\MiddlewareInterface;

class RouteCollectionBuilder
{

    const MAX_REGEX_GROUP_SIZE = 20;
    private RouteCollection $routeCollection;
    // TODO cache route collection


    public function __construct(?RouteCollection $routeCollection = null)
    {
        $this->routeCollection = $routeCollection !== null ? $routeCollection : new RouteCollection();
    }


    /**
     * @param MiddlewareInterface[] $middleware
     */
    public function map(string $method, string $path, string $handler, array $middleware = []): self
    {
        $pathData = $this->preparePathData($path, $method, $handler);

        if ($pathData->hasVariables()) {
            $regexGroup = $this->routeCollection->getLastRegexGroup();

            if (count($regexGroup->routeMap) >= self::MAX_REGEX_GROUP_SIZE) {
                $regexGroup = new RegexGroup();
                $this->routeCollection->regex[] = $regexGroup;
            }

            $regexGroup->map($path, $pathData);
        } else {
            $this->routeCollection->static[$path] = $pathData;
        }

        return $this;
    }

    public function preparePathData(string $path, string $method, string $handler): PathData
    {
        $matches = [];
        preg_match_all('/{([a-zA-y\d_-]*)}/', $path, $matches);
        $variables = $matches[1] ?? [];

        return new PathData($method, $handler, $variables);
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->routeCollection;
    }

}