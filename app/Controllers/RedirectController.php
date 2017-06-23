<?php
/**
 * Created by PhpStorm.
 * User: savvaniss
 * Date: 6/22/17
 * Time: 4:41 PM
 */

namespace App\Controllers;

use App\Models\Url;

class RedirectController
{

    public function index($request, $response){
        //get only the shortUrl path to search it in database
        $url=$request->getAttribute('routeInfo')[2]['Url'];
        //search it in database
        $data=$this->existIndatabase($url);
        //if not found redirect to home page with the error
        if(!$data){
            $this->flash->addMessage('danger', 'This Url does not exist');
            return $response->withRedirect($this->router->pathFor('home'));
        }
        //else shortUrl found make the redirect
        return $response->withRedirect($data->longurl);

    }

    public function existIndatabase($url){
        $data = Url::where('shorturl', $url)->first();
        if ($data === null) {
            return false;

        }
        return $data;

    }
}