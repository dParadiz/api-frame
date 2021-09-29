
use ApiFrame\Http\Router;
use Psr\Container\ContainerInterface;

<?php
/**
 * @var \ApiFrame\Http\Router\RouteCollection $collection
 */
?>

return [
    '@router' => fn (ContainerInterface $c) => new Router\Router(
        $c->get('@route_collection'), $c
    ),
    '@route_collection_builder' => fn (ContainerInterface $c) => new Router\RouteCollectionBuilder(
        $c->get('@route_collection')
    ),
    '@route_collection' => fn (ContainerInterface $c) => new Router\RouteCollection(
        [
<?php foreach ($collection->static as $path => $pathData) : ?>
            '<?=$path?>' => new Router\PathData('<?=$pathData->handler?>'),
<?php endforeach; ?>
        ],
        [
<?php foreach ($collection->regex as  $regexGroup) : ?>
            new Router\RegexGroup(
                '<?=$regexGroup->regex?>',
                [
<?php foreach ($regexGroup->routeMap as  $key => $pathData) : ?>
                    '<?=$key?>' => new Router\PathData(
                        '<?=$pathData->handler?>',
                        [
<?php foreach ($pathData->variables as  $variable) : ?>
                            '<?=$variable?>',
<?php endforeach; ?>
                        ]
                    ),
<?php endforeach; ?>
                ]
            ),
<?php endforeach; ?>
        ]
    ),
];