<?php

namespace Api\Http\Router;

class PathData
{
    /**
     * @param string $handler
     * @param string[] $middleware
     * @param string[] $variables
     */
    public function __construct(
        public string $handler,
        public array  $middleware = [],
        public array  $variables = [])
    {
    }
}
