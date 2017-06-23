<?php
/**
 * Created by PhpStorm.
 * User: savvaniss
 * Date: 6/22/17
 * Time: 12:36 PM
 */

namespace App\Controllers;
use App\Models\Url;

class UrlController extends Controller
{
   // protected static $codeset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    public function parseUrl($request, $response){
        $url=$request->getParam('Url');

        //url validations
        //validate url not empty
        if(empty($url)){
            $this->flash->addMessage('danger', 'You did not supply any Url');
            return $response->withRedirect($this->router->pathFor('home'));
        }
        //if url has no http or https add it.
        elseif (!$this->hasHttp($url)){
            $url=$this->addHttp($url);
        }
        //validate that is a url
        elseif (!$this->validateUrl($url)){
            $this->flash->addMessage('warning', ' '.$url .'  is not a url');
            return $response->withRedirect($this->router->pathFor('home'));
        }
        //validate that url response back
        elseif (!$this->validateHostUrl($url)){
            $this->flash->addMessage('warning', 'Url  '.$url .'  not respond');
            return $response->withRedirect($this->router->pathFor('home'));
        }

        //check if url exist in db allready response false or with short
        $shortUrl=$this->urlExist($url);
        if(!$shortUrl){
            // the url does not exist in db. save it to get the id and convert it to shortUrl.
            $urlModel= new Url;
            $urlModel->longurl=$url;
            $urlModel->save();
            //convert id to shortcode
            $urlModel->shorturl=$this->idToShortcode($urlModel->id);
            //save it to db
            $urlModel->save();
            //display short code to user :D
            $this->flash->addMessage('success', 'Your Short Url: '.$request->getUri()->getHost().'/'. $urlModel->shorturl);
            return $response->withRedirect($this->router->pathFor('home'));
        }
        //if exist in db means that short url allready generated for us
        $this->flash->addMessage('success', 'Your Short Url: '.$request->getUri()->getHost().'/'. $shortUrl);
        return $response->withRedirect($this->router->pathFor('home'));


    }


    //function to validate url format
    public function validateUrl($url){
        return filter_var($url, FILTER_VALIDATE_URL,
            FILTER_FLAG_HOST_REQUIRED);
    }

    //function to check that the host response to the url
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

    //check if url exist in database. if yes return the shorturl else return false
    public function urlExist($url){
        $data = Url::where('longurl', $url)->first();
        if ($data === null) {
            return false;

        }
        return $data->shorturl;

    }

    //conver id of a longurl to shortcode
    public function idToShortcode($id){
        $codeset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $base = strlen($codeset);
        $converted = "";

        while ($id > 0) {
            $converted = substr($codeset, ($id % $base), 1) . $converted;
            $id = floor($id/$base);
        }

        return $converted;
    }
    //combine domain with redirect short url
    public function makeFullUrl($request, $url){
        return $request->getUri()->getHost().'/'.$url;
    }

    //check if provided url has http or https and return true or false
    public function hasHttp($url){
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            return false;

        }
        return true;
    }
    //add http to url
    public function addHttp($url){
        return 'http://' . ltrim($url, '/');
    }

}