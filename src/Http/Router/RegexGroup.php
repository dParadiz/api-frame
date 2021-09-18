<?php

namespace Api\Http\Router;

class RegexGroup
{
    /**
     * @param string $regex
     * @param PathData[] $routeMap
     */
    public function __construct(
        public string $regex,
        public array  $routeMap
    )
    {
    }
}
