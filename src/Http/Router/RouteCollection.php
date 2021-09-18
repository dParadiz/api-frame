<?php

namespace Api\Http\Router;

class RouteCollection
{
    /** @var PathData[] */
    public array $static = [];
    /** @var RegexGroup[] */
    public array $regex = [];
}
