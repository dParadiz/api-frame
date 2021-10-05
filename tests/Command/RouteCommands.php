<?php

namespace Command;

use ApiFrame\Command\AddRoute;
use ApiFrame\Command\RemoveRoute;
use ApiFrame\Http\Router\Endpoint;
use ApiFrame\Http\Router\EndpointMap;
use ApiFrame\Http\Router\EndpointMapDiPersister;
use ApiFrame\Http\Router\MapEntry;
use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class RouteCommands extends TestCase
{
    private string $diConfigFile = __DIR__ . '/___router.php';

    public function tearDown(): void
    {
        parent::tearDown();
        unlink($this->diConfigFile);
    }

    public function testAddingNewRoute()
    {
        $commandTester = new CommandTester(new AddRoute($this->diConfigFile));
        $commandTester->execute([
            'path' => '/test/static/path/{id}',
            'handler' => 'handler_class',
            'method' => 'GET'
        ]);

        self::assertEquals(Command::SUCCESS, $commandTester->getStatusCode());

        $di = $this->getDiFromFile($this->diConfigFile);

        $set = $di->get('@endpoint_map');
        self::assertInstanceOf(EndpointMap::class, $set);

        $endpointMatch = $set->match(new Endpoint('GET', '/test/static/path/1'));


        self::assertInstanceOf(MapEntry::class, $endpointMatch);
    }

    public function testRemovingRoute()
    {
        $endpointMap = new EndpointMap();
        $endpointMap->map(new Endpoint('GET', '/test/static/path/{id}'), 'h1');

        $filePersister = new EndpointMapDiPersister();
        $filePersister->persist($endpointMap, $this->diConfigFile);

        $commandTester = new CommandTester(new RemoveRoute($this->diConfigFile));
        $commandTester->execute([
            'path' => '/test/static/path/{id}',
            'method' => 'GET'
        ]);

        self::assertEquals(Command::SUCCESS, $commandTester->getStatusCode());

        $di = $this->getDiFromFile($this->diConfigFile);

        $endpointMap = $di->get('@endpoint_map');
        self::assertInstanceOf(EndpointMap::class, $endpointMap);


        $mapEntry = $endpointMap->match(new Endpoint('GET', '/test/static/path/{id}'));

        self::assertNull($mapEntry);
    }


    private function getDiFromFile(string $fileName): Container
    {
        $definitions = require $fileName;
        return (new ContainerBuilder())->addDefinitions($definitions)->build();
    }
}