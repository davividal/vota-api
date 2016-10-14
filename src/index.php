<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\VarDumperServiceProvider(), [
    'var_dumper.dump_destination' => __DIR__ . '/../logs/development.log',
]);
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__.'/../logs/development.log',
]);


$app->get('/voters', function (Request $request) use ($app) {
    $controller = new Controller\VotersController($app);
    return new Response(json_encode($controller->index()));
});

$app->post('/api/register-voter', function (Request $request) use ($app) {
    $controller = new Controller\VotersController($app);
    $msg = $controller->register($request);
    return new Response(json_encode(['message' => $msg]));
});

$app->post('/api/login', function (Request $request) use ($app) {
    $controller = new Controller\VotersController($app);
    $msg = $controller->login($request, $app);
    $app['monolog']->addDebug('Login: ' . $msg);
    return new Response(json_encode(['message' => $msg]));
});

$app->run();
