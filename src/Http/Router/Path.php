<?php

namespace ApiFrame\Http\Router;

class Path
{

    public function __construct(
        private string $urlPath,
        private string $method
    )
    {
    }

    public function __toString(): string
    {
        return "{$this->method}@{$this->urlPath}";
    }
}