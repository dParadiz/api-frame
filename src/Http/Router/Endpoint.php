<?php

namespace ApiFrame\Http\Router;

class Endpoint
{

    public function __construct(
        public string $method,
        public string $path
    )
    {
    }

    public function hasVariables(): bool
    {
        return $this->variablesCount() > 0;
    }

    public function variablesCount(): int
    {
        preg_match_all('/{([a-zA-y\d_-]*)}/', $this->path, $matches);
        return count($matches[1] ?? []);
    }

    public function isSameAs(Endpoint $pathData): bool
    {
        return $this->path === $pathData->path
            && $this->method === $pathData->method;
    }

    public function __toString(): string
    {
        return "{$this->method}@{$this->path}";
    }
}
