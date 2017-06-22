<?php

namespace App\Controllers;

//use Slim\Http\Uri;

class HomeController extends Controller
{
    public function index($request, $response){

        return $this->view->render($response, 'home.twig');
    }

}