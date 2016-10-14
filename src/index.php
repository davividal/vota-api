<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\VarDumperServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__.'/../logs/development.log',
]);

$app['voters.controller'] = function() use ($app) {
    $request = $app['request_stack']->getCurrentRequest();
    return new Controller\VotersController($app, $request);
};

$app->post('/api/login', 'voters.controller:login');
$app->post('/api/register-voter', 'voters.controller:register');

$app->get('/voters', 'voters.controller:index');

$app->run();
