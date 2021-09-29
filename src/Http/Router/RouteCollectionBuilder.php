<?php

namespace ApiFrame\Http\Router;

use RuntimeException;

class RouteCollectionBuilder
{
    private int $maxRegexGroupSize = 20;
    private RouteCollection $collection;

    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    public function maxRegexGroupSize(int $size): self
    {
        $this->maxRegexGroupSize = $size;

        return $this;
    }

    public function map(string $path, string $handler, string $method = ''): self
    {
        $pathData = $this->preparePathData($path, $handler);

        $path = new Path($path, $method);

        $matchedPathData = $this->collection->match($path);
        if ($matchedPathData instanceof PathData && !$matchedPathData->isSameAs($pathData)) {
            throw new RuntimeException('Path with different setup already exist');
        }

        if ($pathData->hasVariables()) {
            $regexGroup = $this->collection->getLastRegexGroup();

            if (count($regexGroup->routeMap) >= $this->maxRegexGroupSize) {
                $regexGroup = new RegexGroup();
                $this->collection->regex[] = $regexGroup;
            }

            $regexGroup->map($path, $pathData);
        } else {
            $this->collection->static[(string)$path] = $pathData;
        }

        return $this;
    }

    public function remove(string $path, string $method = ''): void
    {
        throw new RuntimeException('Implement');
    }

    public function preparePathData(string $path, string $handler): PathData
    {
        $matches = [];
        preg_match_all('/{([a-zA-y\d_-]*)}/', $path, $matches);
        $variables = $matches[1] ?? [];

        return new PathData($handler, $variables);
    }

    public function getCollection(): RouteCollection
    {
        return $this->collection;
    }

}