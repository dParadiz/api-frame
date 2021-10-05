<?php

namespace ApiFrame\Http\Router;

use RuntimeException;

final class EndpointMap
{
    /**
     * @param MapEntry[] $static
     * @param RegexGroup[] $regex
     */
    public function __construct(
        private array $static = [],
        private array $regex = [],
        private int   $maxRegexGroupSize = 20
    )
    {

    }

    public function map(Endpoint $endpoint, string $handler): self
    {
        $matchedEndpoint = $this->match($endpoint);
        if ($matchedEndpoint instanceof Endpoint && !$matchedEndpoint->isSameAs($endpoint)) {
            throw new RuntimeException('Path with different setup already exist');
        }

        if ($endpoint->variablesCount() > 0) {
            $regexGroup = $this->getLastRegexGroup();

            if (count($regexGroup->routeMap) >= $this->maxRegexGroupSize) {
                $regexGroup = new RegexGroup();
                $this->regex[] = $regexGroup;
            }

            $regexGroup->add($endpoint, $handler);
        } else {
            $this->static[(string)$endpoint] = new MapEntry($endpoint, $handler);
        }

        return $this;
    }

    public function match(Endpoint $endpoint): ?MapEntry
    {
        return $this->static[(string)$endpoint] ?? $this->getPathDataFromRegexGroups($endpoint);
    }

    private function getPathDataFromRegexGroups(Endpoint $endpoint): ?MapEntry
    {
        foreach ($this->regex as $routeChunk) {
            $pathData = $routeChunk->match($endpoint);

            if ($pathData instanceof MapEntry) {
                return $pathData;
            }
        }

        return null;
    }

    private function getLastRegexGroup(): RegexGroup
    {
        $lastKey = array_key_last($this->regex);

        if ($lastKey !== null) {
            $regexGroup = $this->regex[$lastKey];
        } else {
            $regexGroup = new RegexGroup();
            $this->regex[] = $regexGroup;
        }

        return $regexGroup;
    }

    /**
     * @return array<string, MapEntry>
     */
    public function getStaticEndpoints(): array
    {
        return $this->static;
    }

    /**
     * @return RegexGroup[]
     */
    public function getRegexGroups(): array
    {
        return $this->regex;
    }

    public function remove(Endpoint $endpoint): bool
    {
        if (isset($this->static[(string)$endpoint])) {
            unset($this->static[(string)$endpoint]);
            return true;
        } else {
            foreach ($this->regex as $regexGroup) {
                if ($regexGroup->remove($endpoint)) {
                    return true;
                };
            }
        }
        return false;
    }
}
