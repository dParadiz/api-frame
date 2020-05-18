<?php

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';
$env = (string)getenv('ENVIRONMENT');

$builder = (new ContainerBuilder) ->addDefinitions(
    __DIR__ . '/../config/services.php'
);

try {
    $container = $builder->build();
} catch (Exception $e) {
    http_response_code(500);
    echo $env === 'develop' ? $e->getMessage() : 'Internal server error';
    exit;
}

$request = $container->get(\Psr\Http\Message\ServerRequestInterface::class);
$dispatcher = $container->get(Api\Http\Dispatcher::class);
$dispatcher->dispatch($request);
