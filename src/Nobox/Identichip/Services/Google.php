<?php
/**
 * This is a Google API wrapper to the google/apiclient PHP Library
 * for Twitter's REST API
 * This wrapper is  easiest use inside Identichip package.
 * Author: Alberto Estrada Puerta
 */

namespace Nobox\Identichip\Services;


use \Config;
use \Log;
use \Redirect;
use \Session;

use \Google_Client;
use \Google_Service_Oauth2;


class Google {

    private $config;
    public $client;

    public function __construct()
    {
        $this->config = Config::get('identichip::consumers.Google');
        $this->client = new Google_Client;

        //App Config
        $this->client->setApplicationName($this->config['app_name']);
        $this->client->setClientId($this->config['client_id']);
        $this->client->setClientSecret($this->config['client_secret']);
        $this->client->setScopes($this->config['scope']);
    }



    public function getAuthURL()
    {
        $authURL = $this->client->createAuthUrl();
        return Redirect::to((string)$authURL);
    }


    public function setService()
    {
        $this->service = new Google_Service_Oauth2($this->client);
    }

    public function getUser()
    {
        $user_info = $this->service->userinfo->get();
        return $user_info;
    }

}
