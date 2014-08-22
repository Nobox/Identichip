<?php
/**
 * This is a Twitter wrapper to the kertz/twitteroauth PHP Library
 * for Twitter's REST API
 * This wrapper is  easiest use inside Identichip package.
 * Author: Alberto Estrada Puerta
 */

namespace Nobox\Identichip\Services;


use \Config;
use \Log;
use \Redirect;
use \Session;

use \TwitterOAuth;

class Twitter {

    private $connection;
    private $config;

    public function __construct()
    {
        $this->config = Config::get('identichip::consumers.Twitter');

        $this->connection = new TwitterOAuth (
            $this->config['client_id'],
            $this->config['client_secret']
        );
    }


    public function getAuthUrl($redirect)
    {
        $temporary_credentials = $this->connection->getRequestToken($redirect);
        Session::put('token', $temporary_credentials['oauth_token']);
        Session::put('secret', $temporary_credentials['oauth_token_secret']);
        $redirect_url = $this->connection->getAuthorizeURL($temporary_credentials['oauth_token']);


        return Redirect::to((string)$redirect_url);
    }


    public function getUser($token, $secret, $verifier)
    {
        $this->connection = new TwitterOAuth(
            $this->config['client_id'],
            $this->config['client_secret'],
            $token,
            $secret
        );

        $this->connection->getAccessToken($verifier);

    }

    public function doTwitterRequest($method, $request)
    {
        $result = $this->connection->{$method}($request);
        return $result;
    }

}
