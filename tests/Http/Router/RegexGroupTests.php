<?php

namespace Http\Router;


use ApiFrame\Http\Router\Endpoint;
use ApiFrame\Http\Router\MapEntry;
use ApiFrame\Http\Router\RegexGroup;
use PHPUnit\Framework\TestCase;

class RegexGroupTests extends TestCase
{
    /** @test */
    public function endpoint_can_be_removed()
    {
        [$test1, $test2] = $this->getRandomEndpointsTestData(2);

        $regexGroup = (new RegexGroup())
            ->add($test1['endpoint'], $test1['handler'])
            ->add($test2['endpoint'], $test2['handler']);

        $regexGroup->remove($test1['endpoint']);

        $pathData1 = $regexGroup->match($test1['endpoint']);
        self::assertNull($pathData1);
        self::assertStringNotContainsString($test1['handler'], $regexGroup->regex);

        $pathData2 = $regexGroup->match($test2['endpoint']);
        self::assertInstanceOf(MapEntry::class, $pathData2);

    }

    /** @test */
    public function multiple_endpoint_can_be_added()
    {
        $tests = $this->getRandomEndpointsTestData(50);

        $regexGroup = new RegexGroup();
        foreach ($tests as $test) {
            $regexGroup->add($test['endpoint'], $test['handler']);
        }

        foreach ($tests as $test) {
            $matchedEndpoint = $regexGroup->match($test['endpoint']);
            self::assertInstanceOf(MapEntry::class, $matchedEndpoint);
            self::assertTrue($matchedEndpoint->endpoint->isSameAs($test['endpoint']));
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