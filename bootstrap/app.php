<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
require __DIR__ . '/../vendor/autoload.php';

use Noodlehaus\Config;
$config = new Config(__DIR__ . '/../app/config');


session_start();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => $config->get('mysql.driver'),
            'host' => $config->get('mysql.host'),
            'database' => $config->get('mysql.database'),
            'username' => $config->get('mysql.username'),
            'password' => $config->get('mysql.password'),
            'charset' =>  $config->get('mysql.charset'),
            'collation' => $config->get('mysql.collation'),
            'prefix' => $config->get('mysql.prefix'),
        ]
    ],
]);
$container = $app->getContainer();

//database connect
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

//add db inside container
$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};


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

//register Url controller
$container['UrlController'] = function ($container) {
    return new \App\Controllers\UrlController($container);
};

//register Redirect controller
$container['RedirectController'] = function ($container) {
    return new \App\Controllers\RedirectController($container);
};


require __DIR__ . '/../app/routes.php';