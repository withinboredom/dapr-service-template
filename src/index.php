<?php

use Dapr\ActorRuntime;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/actors/example.php';

define('ACTOR_CONFIG', [
    'entities' => ['ExampleActor']
]);
define('ACTOR_NAMESPACE', '/Example\Actors');

function is_healthy()
{
    // todo: set to 500 if not healthy
    $http_response_header(200);
}

$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) {
        $r->addGroup(
            '/dapr',
            function (FastRoute\RouteCollector $r) {
                $r->addRoute(
                    'GET',
                    '/config',
                    function () {
                        ActorRuntime::config(ACTOR_CONFIG);
                    }
                );
            }
        );
        $r->addRoute('GET', '/healthz', 'is_healthy');
        $r->addRoute(
            ['GET', 'POST', 'PUT'],
            '/actors.*',
            function () {
                ActorRuntime::invoke(ACTOR_NAMESPACE);
            }
        );
    }
);

$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$route_info = $dispatcher->dispatch($method, $uri);
switch ($route_info[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        header('Allow: '.implode(', ', $route_info[1]));
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $route_info[1];
        $vars    = $route_info[2];
        $handler($vars);
        break;
}
