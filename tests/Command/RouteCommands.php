<?php

namespace Command;

use ApiFrame\Command\AddRoute;
use ApiFrame\Command\RemoveRoute;
use ApiFrame\Http\Router\Router;
use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
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
        $di->set('handler_class', $this->getMockBuilder(RequestHandlerInterface::class)->getMock());

        $router = $di->get('@router');
        self::assertInstanceOf(Router::class, $router);

        $request = $this->reqeust('/test/static/path/1', 'GET');
        $handler = $router->getRequestHandler($request);

        self::assertInstanceOf(RequestHandlerInterface::class, $handler);
    }

    public function testRemovingRoute()
    {
        $commandTester = new CommandTester(new RemoveRoute($this->diConfigFile));
        $commandTester->execute([
            'path' => '/test/static/path/{id}',
            'method' => 'GET'
        ]);

        self::assertEquals(Command::SUCCESS, $commandTester->getStatusCode());

        $di = $this->getDiFromFile($this->diConfigFile);
        $di->set('handler_class', $this->getMockBuilder(RequestHandlerInterface::class)->getMock());

        $router = $di->get('@router');
        self::assertInstanceOf(Router::class, $router);

        $request = $this->reqeust('/test/static/path/1', 'GET');
        $handler = $router->getRequestHandler($request);

        self::assertInstanceOf(RequestHandlerInterface::class, $handler);
    }

    private function reqeust(string $path, string $method): ServerRequestInterface
    {
        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn($path);

        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn($uri);

        return $request;
    }

    private function getDiFromFile(string $fileName): Container
    {
        $definitions = require $fileName;
        return (new ContainerBuilder())->addDefinitions($definitions)->build();
    }
}