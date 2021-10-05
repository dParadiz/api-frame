<?php

namespace ApiFrame\Http\Router;

class MapEntry
{
    public function __construct(
        public Endpoint $endpoint,
        public string   $handler
    )
    {

    }
}