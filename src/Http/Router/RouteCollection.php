<?php

namespace Api\Http\Router;

class RouteCollection
{
    /** @var PathData[] */
    public array $static = [];
    /** @var RegexGroup[] */
    public array $regex = [];

    /**
     * @param PathData[] $static
     * @param RegexGroup[] $regex
     */
    public function __construct(array $static = [], array $regex = [])
    {
        $this->static = $static;
        $this->regex = $regex;
    }


    public function getLastRegexGroup(): RegexGroup
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

    public function match(string $path): ?PathData
    {
        return $this->static[$path] ?? $this->getPathDataFromRegexGroups($path);
    }

    private function getPathDataFromRegexGroups(string $path): ?PathData
    {
        foreach ($this->regex as $routeChunk) {
            $pathData = $routeChunk->match($path);

            if ($pathData instanceof PathData) {
                return $pathData;
            }
        }

        return null;
    }
}
