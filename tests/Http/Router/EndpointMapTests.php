<?php

namespace Http\Router;

use ApiFrame\Http\Router\Endpoint;
use ApiFrame\Http\Router\EndpointMap;
use ApiFrame\Http\Router\MapEntry;
use PHPUnit\Framework\TestCase;

class EndpointMapTests extends TestCase
{
    /** @test */
    public function static_endpoint_can_be_added()
    {
        $collection = new EndpointMap();
        $endpoint = new Endpoint('GET', '/test/path');
        $collection->map($endpoint, 'handler');

        $pathData = $collection->match($endpoint);

        self::assertInstanceOf(MapEntry::class, $pathData);
        self::assertEquals('handler', $pathData->handler);
    }

    /** @test */
    public function endpoints_with_different_method_but_same_path_can_be_added()
    {
        $collection = new EndpointMap();
        $path = '/test/uri';
        $postEndpoint = new Endpoint('POST', $path);
        $getEndpoint = new Endpoint('GET', $path);
        $collection->map($getEndpoint, 'getHandler');
        $collection->map($postEndpoint, 'postHandler');

        $match = $collection->match($getEndpoint);

        self::assertInstanceOf(MapEntry::class, $match);
        self::assertEquals('getHandler', $match->handler);

        $match = $collection->match($postEndpoint);

        self::assertInstanceOf(MapEntry::class, $match);
        self::assertEquals('postHandler', $match->handler);
    }

    /** @test */
    public function endpoints_with_variables_can_be_added()
    {
        $collection = new EndpointMap();
        $endpoint = new Endpoint('GET', '/test/uri/{id}');
        $collection->map($endpoint, 'handler');

        $pathData = $collection->match($endpoint);
        self::assertInstanceOf(MapEntry::class, $pathData);

        self::assertEquals('handler', $pathData->handler);
    }


    public function testRouteCollectionCanHandleMultiplePaths()
    {
        $tests = $this->getRandomEndpointsTestData(500);

        $routeCollection = new EndpointMap();
        foreach ($tests as $test) {
            $routeCollection->map($test['endpoint'], $test['handler']);
        }

        foreach ($tests as $test) {

            $pathData = $routeCollection->match($test['endpoint']);

            self::assertInstanceOf(MapEntry::class, $pathData);
            self::assertEquals($test['handler'], $pathData->handler);
        }

    }

    private function getRandomEndpointsTestData(int $num): array
    {
        $endpoints = [];
        $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'HEAD', 'TRACE', 'OPTIONS', 'CONNECT'];
        for ($i = 0; $i <= $num; $i++) {
            $handler = bin2hex(random_bytes(5));
            $numParams = rand(0, 5);
            $variables = [];
            for ($j = 1; $j <= $numParams; $j++) {
                $variables[] = 'param-' . bin2hex(random_bytes(5));
            }
            $pathParts = array_map(fn (string $param) => '{' . $param . '}', $variables);

            $pathParts[] = $handler;
            shuffle($pathParts);


            $endpoints[] = [
                'endpoint' => new Endpoint(
                    $methods[array_rand($methods)],
                    '/' . implode('/', $pathParts),
                ),
                'handler' => $handler
            ];
        }

        return $endpoints;
    }
}