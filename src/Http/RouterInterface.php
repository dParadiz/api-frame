<?php

namespace Api\Http;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function getRequestHandler(ServerRequestInterface $request): array;
}
