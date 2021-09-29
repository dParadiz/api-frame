<?php

namespace ApiFrame\Http\Router;

class PathData
{
    /**
     * @param string[] $variables
     */
    public function __construct(
        public string $handler,
        public array  $variables = [])
    {
    }

    public function hasVariables(): bool
    {
        return count($this->variables) > 0;
    }

    public function isSameAs(PathData $pathData): bool
    {
        return $this->handler === $pathData->handler
            && array_diff($this->variables, $pathData->variables) === [];
    }
}
