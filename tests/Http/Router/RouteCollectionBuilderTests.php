<?php

namespace Http\Router;

use Api\Http\Router\PathData;
use Api\Http\Router\RouteCollectionBuilder;
use PHPUnit\Framework\TestCase;

class RouteCollectionBuilderTests extends TestCase
{

    public function testUserCanAddStaticRoute()
    {
        $routeCollectionBuilder = new RouteCollectionBuilder();
        $path = '/test/uri';
        $routeCollectionBuilder->map('GET', $path, 'handler');

        $routeCollection = $routeCollectionBuilder->getRouteCollection();

        $pathData = $routeCollection->match($path);

        self::assertInstanceOf(PathData::class, $pathData);
        self::assertEquals('GET', $pathData->method);
        self::assertEquals('handler', $pathData->handler);
    }

    public function testUserCanAddDynamicRoute()
    {
        $routeCollectionBuilder = new RouteCollectionBuilder();
        $path = '/test/uri/{id}';
        $routeCollectionBuilder->map('GET', $path, 'handler');

        $routeCollection = $routeCollectionBuilder->getRouteCollection();

        $pathData = $routeCollection->match('/test/uri/123');

        self::assertInstanceOf(PathData::class, $pathData);
        self::assertContains('id', $pathData->variables);
        self::assertEquals('GET', $pathData->method);
        self::assertEquals('handler', $pathData->handler);
    }


    public function testRouteCollectionCanHandleMultiplePaths()
    {
        $paths = $this->getRandomPaths(500);

        $routeCollectionBuilder = new RouteCollectionBuilder();
        foreach ($paths as $path) {
            $routeCollectionBuilder->map($path['method'], $path['path'], $path['handler']);
        }

        $routeCollection = $routeCollectionBuilder->getRouteCollection();

        foreach ($paths as $path) {

            $pathData = $routeCollection->match($path['matchPath']);

            self::assertInstanceOf(PathData::class, $pathData);
            foreach ($path['variables'] as $variable) {
                self::assertContains($variable, $pathData->variables);
            }
            self::assertEquals($path['method'], $pathData->method);
            self::assertEquals($path['handler'], $pathData->handler);
        }

    }

    private function getRandomPaths(int $num = 1): array
    {
        $paths = [];
        $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'HEAD', 'TRACE', 'OPTIONS', 'CONNECT'];
        for ($i = 0; $i <= $num; $i++) {
            $numParams = rand(0, 5);
            $variables = [];
            for ($j = 1; $j <= $numParams; $j++) {
                $variables[] = 'param-' . bin2hex(random_bytes(5));
            }
            $pathParts = array_map(fn (string $param) => '{' . $param . '}', $variables);

            $pathParts[] = bin2hex(random_bytes(5));
            shuffle($pathParts);

            $matchParts = array_map(
                fn (string $part) => $part[0] === '{' ? bin2hex(random_bytes(5)) : $part,
                $pathParts
            );

            $paths[] = [
                'path' => '/' . implode('/', $pathParts),
                'matchPath' => '/' . implode('/', $matchParts),
                'method' => $methods[array_rand($methods)],
                'variables' => $variables,
                'handler' => 'handler_' . bin2hex(random_bytes(5)),
            ];
        }

        return $paths;
    }
}
