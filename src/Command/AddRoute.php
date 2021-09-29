<?php

namespace ApiFrame\Command;

use ApiFrame\Http\Router\RouteCollection;
use ApiFrame\Http\Router\RouteCollectionBuilder;
use ApiFrame\Http\Router\RouteCollectionDiPersister;
use DI\ContainerBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddRoute extends Command
{
    protected static $defaultName = 'router:add-route';
    private string $routeConfigFile;

    public function __construct(string $routeConfigFile)
    {
        parent::__construct(self::$defaultName);
        $this->routeConfigFile = $routeConfigFile;
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'Uri to match to handler')
            ->addArgument('handler', InputArgument::REQUIRED, 'Handler class name or di reference')
            ->addArgument('method', InputArgument::OPTIONAL, 'HTTP method to match');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string)$input->getArgument('path');
        $handler = (string)$input->getArgument('handler');
        $method = (string)$input->getArgument('method');

        if (!file_exists($this->routeConfigFile)) {
            $this->saveRouterConfig($this->routeConfigFile, new RouteCollection());
        }

        $routeDefinitions = require $this->routeConfigFile;

        $di = (new ContainerBuilder())->addDefinitions($routeDefinitions)->build();

        $routeCollectionBuilder = $di->get('@route_collection_builder');

        if (!($routeCollectionBuilder instanceof RouteCollectionBuilder)) {
            throw new \RuntimeException('@route_collection_builder is not instance of RouteCollectionBuilder ');
        }

        $routeCollectionBuilder->map($path, $handler, $method);

        $this->saveRouterConfig($this->routeConfigFile, $routeCollectionBuilder->getCollection());


        return Command::SUCCESS;
    }

    private function saveRouterConfig(string $filename, RouteCollection $collection): void
    {
        $filePersister = new RouteCollectionDiPersister();

        $filePersister->persist($collection, $filename);
    }
}