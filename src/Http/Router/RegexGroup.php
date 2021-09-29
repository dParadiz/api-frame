<?php

namespace ApiFrame\Http\Router;

class RegexGroup
{
    /**
     * @param string $regex
     * @param PathData[] $routeMap
     */
    public function __construct(
        public string $regex = '~^(?)$~',
        public array  $routeMap = []
    )
    {
    }

    public function map(string $path, PathData $pathData): void
    {
        $numVariables = count($pathData->variables);
        $lastGroupNumber = (int)array_key_last($this->routeMap);
        $groupNumber = max($lastGroupNumber, $numVariables);

        $regex = $this->pathToRegex($path, $groupNumber - $numVariables);
        $this->regex = substr($this->regex, 0, -3) . "|$regex)$~";
        $this->routeMap[$groupNumber + 1] = $pathData;
    }

    private function pathToRegex(string $path, int $matchOffset): string
    {
        return preg_replace('/{[^}]*}/', '([^/]*)', $path) . str_repeat('()', $matchOffset);
    }

    public function match(string $path): ?PathData
    {
        if (preg_match($this->regex, $path, $matches) === 1) {
            return $this->routeMap[count($matches)] ?? null;
        }

        return null;
    }

}
