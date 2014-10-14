<?php
/**
 * This is a Instagram wrapper to the "php-instagram-api/php-instagram-api": "dev-master" PHP Library
 * for 's  API
 * This wrapper is  easiest use inside Identichip package.
 * Author: Alberto Estrada Puerta
 */

namespace Nobox\Identichip\Services;


use \Config;
use \Log;
use \Redirect;
use \Session;
use \Input;

class Instagram {

    private $connection;
    private $config;

    public function __construct()
    {
        $this->config = Config::get('identichip::consumers.Instagram');
        $this->connection = new \Instagram\Auth( $this->config );
    }


    public function getRedirectURL()
    {
        return $this->connection->authorize();
    }


    public function storeSession()
    {
        Session::put('instagram_access_token',$this->connection->getAccessToken( Input::get('code') ));
    }


    public function getUser()
    {
        $instagram = new \Instagram\Instagram;
        $instagram->setAccessToken( Session::get('instagram_access_token') );
        $current_user = $instagram->getCurrentUser();

        return $current_user;
    }



}
