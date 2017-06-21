<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
require __DIR__ . '/../vendor/autoload.php';



session_start();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ],
]);
$container = $app->getContainer();

//for flash messages
$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages;
};

//for twig views
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
        //enable debug
        'debug' => true
    ]);
    //enable debug
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    //for flash messages
    $view->getEnvironment()->addGlobal('flash', $container->flash);
    return $view;
};

//register home controller
$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

require __DIR__ . '/../app/routes.php';