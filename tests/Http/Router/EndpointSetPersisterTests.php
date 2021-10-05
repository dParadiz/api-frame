<?php

namespace Http\Router;

use ApiFrame\Http\Router\Endpoint;
use ApiFrame\Http\Router\EndpointMap;
use ApiFrame\Http\Router\EndpointMapDiPersister;
use ApiFrame\Http\Router\MapEntry;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class EndpointSetPersisterTests extends TestCase
{


    /** @return MapEntry[] */
    private function getRandomEndpointMatches(int $num = 1): array
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


            $paths[] = new MapEntry(
                new Endpoint($methods[array_rand($methods)], '/' . implode('/', $pathParts)),
                'handler_' . bin2hex(random_bytes(5))
            );
        }

        return $paths;
    }

    public function testDiConfigGeneration()
    {
        $randomEndpointMatches = $this->getRandomEndpointMatches(100);

        $set = new EndpointMap();
        foreach ($randomEndpointMatches as $match) {
            $set->map($match->endpoint, $match->handler);
        }

        $fileName = __DIR__ . '/routes.php';


        $persister = new EndpointMapDiPersister();
        $persister->persist($set, $fileName);

        $definitions = require $fileName;
        $container = (new ContainerBuilder())->addDefinitions($definitions)->build();
        unlink($fileName);

        $collection = $container->get('@endpoint_map');

        self::assertInstanceOf(EndpointMap::class, $collection);

        foreach ($randomEndpointMatches as $match) {

            $matchedEndpoint = $collection->match($match->endpoint);

            self::assertInstanceOf(MapEntry::class, $matchedEndpoint);

            self::assertEquals($match->handler, $matchedEndpoint->handler);
        }
    }
}
