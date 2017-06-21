<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
require __DIR__ . '/../vendor/autoload.php';



//configuration values
session_start();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ],
]);
$container = $app->getContainer();

//testing config files
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
    return $view;
};

require __DIR__ . '/../app/routes.php';