<?php

/**
 * #Adding static route
 *
 * $routeCollection->static['/path'] = new \Api\Http\Router\PathData("requestHandler", ["Middleware"]);
 *
 * #Adding regex route
 *
 * $routeCollection->regex['/path/{id}'] = new \Api\Http\Router\RegexGroup(
 *   'regex',
 *   [new \Api\Http\Router\PathData("requestHandler", ["Middleware"], ['id'])]
 * );
 */

$routeCollection = new \Api\Http\Router\RouteCollection();


