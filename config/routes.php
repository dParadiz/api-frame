<?php declare(strict_types=1);

use ApiFrame\Http\Router;
use Psr\Container\ContainerInterface;

return [
    '@router' => fn (ContainerInterface $c) => new Router\Router(
        $c->get('@route_collection'), $c
    ),
    '@route_collection_builder' => fn (ContainerInterface $c) => new Router\RouteCollectionBuilder(
        $c->get('@route_collection')
    ),
    '@route_collection' => fn (ContainerInterface $c) => new Router\RouteCollection(),
];
