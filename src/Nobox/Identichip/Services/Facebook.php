<?php
/**
 * This is a facebook sdk php wrapper
 * for easiest use inside Identichip package.
 * Author: Alberto Estrada Puerta
 */

namespace Nobox\Identichip\Services;


use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;
use \Config;
use \Log;
use \Redirect;

session_start();

class Facebook {

    public function __construct()
    {
        $config = Config::get('identichip::consumers.Facebook');

        FacebookSession::setDefaultApplication(
            $config['client_id'],
            $config['client_secret']
        );

    }

    
    public function getAuthURL($redirect_url)
    {
        $helper = new FacebookRedirectLoginHelper($redirect_url);
        $loginUrl = $helper->getLoginUrl();

        return Redirect::to((string)$loginUrl);
    }


    public function facebokLoginHelper($redirect_url, $canvas = false)
    {

        if($canvas){
            $helper = new FacebookCanvasLoginHelper();
        }
        else{
            $helper = new FacebookRedirectLoginHelper($redirect_url);
        }
        //missing javascript helper to be added in other relase

        // log errors
        try {
          $session = $helper->getSessionFromRedirect();
        } catch(FacebookRequestException $ex) {
          // When Facebook returns an error
          Log::error('Something is wrong from facebook.');
          return false;

        } catch(\Exception $ex) {
          // When validation fails or other local issues
          Log::error('Something is wrong with your user validation.');
          return false;

        }

        // work with the session
        if($session)
        {
            return $session;
        }
        else{
            //return false to trigger redirect to facebook
            return false;
        }

    }


    public function doFacebookRequest($session, $method, $request)
    {
        $result = (new FacebookRequest(
        $session, $method, $request
        ))->execute()->getGraphObject(GraphUser::className());

        return $result;
    }



}
