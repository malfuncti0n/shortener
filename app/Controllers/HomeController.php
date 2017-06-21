<?php

namespace App\Controllers;


class HomeController extends Controller
{
    public function index($request, $response){
        //test flash functionality
        $this->flash->addMessage('info', 'Flash messages are working');
        return $this->view->render($response, 'home.twig');
    }
}