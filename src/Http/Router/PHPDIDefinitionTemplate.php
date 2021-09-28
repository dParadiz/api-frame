<?php
/**
 * @var \Api\Http\Router\RouteCollection $collection
 * @var string $containerServiceName
 */
?>

return [
    '<?=$containerServiceName; ?>' => fn (\Psr\Container\ContainerInterface $c) => new \Api\Http\Router\RouteCollection(
        [
<?php foreach ($collection->static as $path => $pathData) : ?>
            '<?=$path?>' => new \Api\Http\Router\PathData('<?=$pathData->method?>', '<?=$pathData->handler?>'),
<?php endforeach; ?>
        ],
        [
<?php foreach ($collection->regex as  $regexGroup) : ?>
            new \Api\Http\Router\RegexGroup(
                '<?=$regexGroup->regex?>',
                [
<?php foreach ($regexGroup->routeMap as  $key => $pathData) : ?>
                    '<?=$key?>' => new \Api\Http\Router\PathData(
                        '<?=$pathData->method?>',
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