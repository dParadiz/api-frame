
use ApiFrame\Http\Router;
use Psr\Container\ContainerInterface;

<?php
/**
 * @var \ApiFrame\Http\Router\EndpointMap $set
 */
?>

return [
    '@endpoint_map' => fn (ContainerInterface $c) => new Router\EndpointMap(
        [
<?php foreach ($set->getStaticEndpoints() as $path => $endpointMatch) : ?>
            '<?=$path?>' => new Router\MapEntry(
                new Router\Endpoint(
                    '<?= $endpointMatch->endpoint->method;?>',
                    '<?= $endpointMatch->endpoint->path;?>',
                ),
                '<?=$endpointMatch->handler?>'
            ),
<?php endforeach; ?>
        ],
        [
<?php foreach ($set->getRegexGroups() as $regexGroup) : ?>
            new Router\RegexGroup(
                '<?=$regexGroup->regex?>',
                [
<?php foreach ($regexGroup->routeMap as  $key => $endpointMatch) : ?>
                    '<?=$key?>' => new Router\MapEntry(
                        new Router\Endpoint(
                            '<?= $endpointMatch->endpoint->method;?>',
                            '<?= $endpointMatch->endpoint->path;?>',
                        ),
                        '<?=$endpointMatch->handler?>',
                    ),
<?php endforeach; ?>
                ]
            ),
<?php endforeach; ?>
        ]
    ),
];