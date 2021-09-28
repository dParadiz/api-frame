<?php

namespace Api\Http\Router;

class RouteCollectionBuilder
{
    private int $maxRegexGroupSize = 20;
    private RouteCollection $collection;

    public function __construct()
    {
        $this->collection = new RouteCollection();
    }

    public function maxRegexGroupSize(int $size): self
    {
        $this->maxRegexGroupSize = $size;

        return $this;
    }

    public function use(RouteCollection $routeCollection): self
    {
        $this->collection = $routeCollection;

        return $this;
    }


    public function map(string $path, string $handler, string $method = ''): self
    {
        $pathData = $this->preparePathData($path, $method, $handler);

        if ($pathData->hasVariables()) {
            $regexGroup = $this->collection->getLastRegexGroup();

            if (count($regexGroup->routeMap) >= $this->maxRegexGroupSize) {
                $regexGroup = new RegexGroup();
                $this->collection->regex[] = $regexGroup;
            }

            $regexGroup->map($path, $pathData);
        } else {
            $this->collection->static[$path] = $pathData;
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

    public function getCollection(): RouteCollection
    {
        return $this->collection;
    }

}