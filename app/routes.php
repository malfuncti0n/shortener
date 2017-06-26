<?php

// Define app routes

$app->get('/', 'HomeController:index')->setName('home');
$app->post('/', 'UrlController:parseUrl');
$app->get('/{Url}', 'RedirectController:index')->setName('redirect');

//unauthenticate routes for signup and signin
$app->group('', function () {
    $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', 'AuthController:postSignUp');
    $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));


$app->group('', function () {
    //for authentications
    $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');
    $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', 'PasswordController:postChangePassword');;
})->add(new AuthMiddleware($container));