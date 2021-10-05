<?php

namespace ApiFrame\Http\Router;

final class RegexGroup
{
    private const INITIAL_REGEX_PATTERN = '~^(?)$~';

    /**
     * @param string $regex
     * @param MapEntry[] $routeMap
     */
    public function __construct(
        public string $regex = self::INITIAL_REGEX_PATTERN,
        public array  $routeMap = []
    )
    {
    }

    public function add(Endpoint $endpoint, string $handler): self
    {
        $numVariables = $endpoint->variablesCount();
        $lastGroupNumber = (int)array_key_last($this->routeMap);
        $groupNumber = max($lastGroupNumber, $numVariables);

        $regex = $this->pathToRegex((string)$endpoint, $groupNumber - $numVariables);
        $this->regex = substr($this->regex, 0, -3) . "|$regex)$~";
        $this->routeMap[$groupNumber + 1] = new MapEntry($endpoint, $handler);

        return $this;
    }

    private function pathToRegex(string $path, int $matchOffset): string
    {
        return preg_replace('/{[^}]*}/', '([^/]*)', $path) . str_repeat('()', $matchOffset);
    }

    public function match(Endpoint $endpoint): ?MapEntry
    {
        if (preg_match($this->regex, (string)$endpoint, $matches) === 1) {
            return $this->routeMap[count($matches)] ?? null;
        }

        return null;
    }

    public function remove(Endpoint $endpoint): bool
    {
        if (preg_match($this->regex, (string)$endpoint, $matches) === 1) {
            unset($this->routeMap[count($matches)]);

            $routeMap = $this->routeMap;
            $this->regex = self::INITIAL_REGEX_PATTERN;
            $this->routeMap = [];

            foreach ($routeMap as $endpointMatch) {
                $this->add($endpointMatch->endpoint, $endpointMatch->handler);
            }
            return true;
        }

        return false;
    }

}
