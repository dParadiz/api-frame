<?php

namespace ApiFrame\Command;

use ApiFrame\Http\Router\Endpoint;
use ApiFrame\Http\Router\EndpointMap;
use ApiFrame\Http\Router\EndpointMapDiPersister;
use DI\ContainerBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveRoute extends Command
{
    protected static $defaultName = 'router:remove-route';
    private string $routeConfigFile;

    public function __construct(string $routeConfigFile)
    {
        parent::__construct(self::$defaultName);
        $this->routeConfigFile = $routeConfigFile;
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'Uri to match to handler')
            ->addArgument('method', InputArgument::OPTIONAL, 'HTTP method to match');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string)$input->getArgument('path');
        $method = (string)$input->getArgument('method');

        if (!file_exists($this->routeConfigFile)) {
            throw new RuntimeException('There is nothing to remove from');
        }

        $routeDefinitions = require $this->routeConfigFile;

        $di = (new ContainerBuilder())->addDefinitions($routeDefinitions)->build();

        $endpointMap = $di->get('@endpoint_map');

        if (!($endpointMap instanceof EndpointMap)) {
            throw new RuntimeException('@endpoint_map is not instance of EndpointMap ');
        }

        $endpointMap->remove(new Endpoint($method, $path));

        $this->saveRouterConfig($this->routeConfigFile, $endpointMap);


        return Command::SUCCESS;
    }

    private function saveRouterConfig(string $filename, EndpointMap $collection): void
    {
        $filePersister = new EndpointMapDiPersister();

        $filePersister->persist($collection, $filename);
    }
}