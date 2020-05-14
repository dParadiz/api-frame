<?php

use function DI\{autowire, create, get};

return [
    'routeCollection' => fn() => require __DIR__ . '/routes.php',
    'request' => fn() => \GuzzleHttp\Psr7\ServerRequest::fromGlobals(),
    \Psr\Http\Message\ServerRequestInterface::class => get('request'),
    Api\Http\Router::class => function (Di\Container $container) {
        return new Api\Http\Router($container->get('routeCollection'), $container);
    },
    Api\Http\RouterInterface::class => get(Api\Http\Router::class),
    Api\Http\Emitter::class => create(),
    Api\Http\EmitterInterface::class => get(Api\Http\Emitter::class),
    Api\Http\Application::class => autowire(),

];
