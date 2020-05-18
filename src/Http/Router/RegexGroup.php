<?php

namespace Api\Http\Router;

class RegexGroup
{
    public string $regex = '';

    /** @var PathData[] */
    public array $routeMap = [];

    /**
     * RegexGroup constructor.
     *
     * @param string $regex
     * @param PathData[] $routeMap
     */
    public function __construct(string $regex, array $routeMap)
    {
        $this->regex = $regex;
        $this->routeMap = $routeMap;
    }

}
