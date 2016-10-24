<?php

// require_once __DIR__.'/vendor/autoload.php';
require_once 'vendor/autoload.php';


$app = new Silex\Application();

$app->register(new Silex\Provider\VarDumperServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(
    new Silex\Provider\MonologServiceProvider(),
    [
        'monolog.logfile' => __DIR__.'/logs/development.log',
    ]
);

$app['eleitores.controller'] = function () use ($app) {
    $request = $app['request_stack']->getCurrentRequest();

    return new Controller\EleitoresController($app, $request);
};

$app['voto.controller'] = function () use ($app) {
    $request = $app['request_stack']->getCurrentRequest();

    return new Controller\VotoController($app, $request);
};

$app->post('/api/login', 'eleitores.controller:login');
$app->post('/api/register-voter', 'eleitores.controller:register');

$app->post('/api/votar', 'voto.controller:votar');
$app
    ->get('/api/resultado-prefeitos/{prefeitoId}', 'voto.controller:resultadoPrefeitos')
    ->value('prefeitoId', null);
$app
    ->get('/api/resultado-vereadores/{vereadorId}', 'voto.controller:resultadoVereadores')
    ->value('vereadorId', null);

$app->post('/api/nova-eleicao', 'voto.controller:novaEleicao');

$app->get('/voters', 'eleitores.controller:index')->bind('voters');
$app->get(
    '/',
    function () use ($app) {
        return $app->redirect($app['url_generator']->generate('voters'));
    }
);

$app->run();
