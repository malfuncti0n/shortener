<?php
/**
 * Created by PhpStorm.
 * User: savvaniss
 * Date: 6/22/17
 * Time: 12:36 PM
 */

namespace App\Controllers;


class UrlController extends Controller
{

    public function parseUrl($request, $response){
        $url=$request->getParam('Url');

        //validate that is a url
        if(!$this->validateUrl($url)){
            $this->flash->addMessage('warning', ' '.$url .'  is not a url');
            return $response->withRedirect($this->router->pathFor('home'));
        }
        //validate tha url response back
        elseif (!$this->validateHostUrl($url)){
            $this->flash->addMessage('warning', 'Flash messages are working');
            $this->flash->addMessage('warning', 'Url  '.$url .'  not respond');
            return $response->withRedirect($this->router->pathFor('home'));
        }
        $this->flash->addMessage('success', 'Url '.$url .' validated succesfully');
        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function validateUrl($url){
        return filter_var($url, FILTER_VALIDATE_URL,
            FILTER_FLAG_HOST_REQUIRED);
    }

    public function validateHostUrl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return (!empty($response) && $response != 404);
    }

}