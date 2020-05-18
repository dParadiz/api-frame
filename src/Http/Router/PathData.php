<?php

namespace Api\Http\Router;

class PathData
{

    public string $handler = '';
    /** @var string[] */
    public array $middleware = [];

    /** @var string[]*/
    public array $variables = [];

    /**
     * PathData constructor.
     *
     * @param string $handler
     * @param string[] $middleware
     * @param string[] $variables
     */
    public function __construct(string $handler, array $middleware = [], array $variables = [])
    {
        $this->handler = $handler;
        $this->middleware = $middleware;
        $this->variables = $variables;
    }

}
