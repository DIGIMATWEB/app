<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Framework\HTTP\Response;
use Framework\Routing\RouteCollection;

App::router()->serve('http://localhost:8080', static function (RouteCollection $routes) : void {
    $routes->namespace('App\Controllers', [
        $routes->get('/', 'Home::index', 'home'),
        $routes->resource('/users', 'Users', 'users', ['replace']),
    ]);
    $routes->notFound(static function () {
        return not_found();
    });
})->serve('http://localhost:8081', static function (RouteCollection $routes) : void {
    $routes->namespace('App\Controllers\API', [
        $routes->get('/', 'Home', 'home'),
        $routes->resource('/users', 'Users', 'users', ['replace']),
    ]);
    $routes->notFound(static function ($args, $req, Response $res) {
        return [
            'status' => [
                'code' => $res->getStatusCode(),
                'reason' => $res->getStatusReason(),
            ],
        ];
    });
}, 'api');
