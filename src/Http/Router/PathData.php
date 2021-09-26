<?php

namespace Api\Http\Router;

class PathData
{
    /**
     * @param string[] $variables
     */
    public function __construct(
        public string $method,
        public string $handler,
        public array  $variables = [])
    {
    }

    public function hasVariables(): bool
    {
        return count($this->variables) > 0;
    }
}
