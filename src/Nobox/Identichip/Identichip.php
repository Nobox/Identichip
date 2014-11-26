<?php

namespace Nobox\Identichip;


use \Config;
use \Input;
use \Auth;
use \Hash;
use \Redirect;
use \Session;
use \Request;

use User;
use Nobox\Identichip\Models\Service as Service;
use Nobox\Identichip\Services\Facebook as Facebook;
use Nobox\Identichip\Services\Twitter as Twitter;
use Nobox\Identichip\Services\Google as Google;


class Identichip{


    public static $errors;

   /**
    * Perform user registration into DB
    * @param array $newUser from user registration form
    * @param mixed value $users_info default is false if not is an object
    * @return mixed values : true if all good | object errors
    */
    public static function register($newUser, $users_info = false)
    {
        $user = new User();
        $user->email = $newUser['email'];
        $user->first_name = $newUser['first_name'];
        $user->last_name = $newUser['last_name'];

        //check if password is null
        //password is not needed for some
        if(isset($newUser['password'])){
            $user->password = Hash::make($newUser['password']);
        }
        else{
            $user->password = Hash::make($newUser['email']);
        }

        if(!$user->isValid())
        {
            self::$errors = $user->errors;
            return false;
        }

        if($users_info && !$users_info->isValid())
        {
            self::$errors = $users_info->errors;
            return false;
        }



        $user->save();

        Auth::login($user);

        return true;
    }


    public static function getErrors()
    {
        return self::$errors;
    }

   /**
    *  Simple Login implementation
    *  this just uses laravel Auth system.
    *  @param array $credentials , contains email/password
    *  @return boolean
    */
    public function login($credentials)
    {

        if (Auth::attempt($credentials)){
            return true;
        }
        else{
            return false;
        }
    }

    /*****              ****\
    *                       *
    *   SERVICES FUNCTIONS  *
    *                       *
    ******              ****/



   /**
    *   Facebook Login/Registration Implementation
    *   This uses Facebook PHP SDK Class Wrapper
    *   @param string $redirect containing the redirect url.
    *   @return Redirect request
    */
    public function facebookLogin($redirect, $canvas = false)
    {
        $current_url = Request::url();
        $facebook = new Facebook;

        $session = $facebook->facebokLoginHelper($current_url, $canvas);

        if ($session) {
            $result = $facebook->doFacebookRequest($session, 'GET', '/me');
            $data = array(
                    'service_id'    => $result->getId(),
                    'name'          => 'facebook',
                    'first_name'    => $result->getFirstName(),
                    'last_name'     => $result->getLastName(),
                    'email'         => $result->getProperty('email'),
                    'access_token'  => $session,
            );

            $data['avatar'] = '//graph.facebook.com/'.$data['service_id'].'/picture?width=200&height=200';

            Session::put('service_info', $data);
            return Redirect::to($redirect);
        }
        else{
            return $facebook->getAuthURL($current_url);
        }


    }


    public function twitterLogin($redirect)
    {
        $twitter = new Twitter;
        $current_url = Request::url();

        $token = Session::pull('token');
        $secret = Session::pull('secret');
        $verifier = Input::get('oauth_verifier');

        // if user cancel app authorization
        // return to the app
        if(Input::has('denied')){
            return Redirect::to($redirect);
        }

        if(isset($token)){
            $twitter->getUser($token, $secret, $verifier);
            $result = $twitter->doTwitterRequest('get', 'account/verify_credentials');

            if(Request::secure()){
                $avatar = $result->profile_image_url_https;
            }
            else{
                $avatar = $result->profile_image_url;
            }

            $final_avatar = str_replace('_normal', '', $avatar);
            $data = array(
                    'service_id'           => $result->id,
                    'name'                 => 'twitter',
                    'first_name'           => $result->name,
                    'last_name'            => '',
                    'email'                => '',
                    'avatar'               => $final_avatar,
                    'access_token'         => $token,
                    'access_token_secret'  => $secret,
                    'handle'               => $result->screen_name,
                    'verifier'             => $verifier
            );

            Session::put('service_info', $data);
            return Redirect::to($redirect);
        }
        else{
            return $twitter->getAuthURL($current_url);
        }

    }



    public function googleLogin()
    {
        $current_url = Request::url();

        $google = new Google;

        $google->client->setRedirectUri($current_url);
        $google->setService();


        // google session token for configuration
        $token = Session::get('google_token');

        // received if the auth callback is positive
        $code = Input::get('code');

        if(isset($code)){
            $google->client->authenticate($code);
            Session::put('google_token', $google->client->getAccessToken());

        }

        if(isset($token))
        {
            $google->client->setAccessToken($token);
        }


        if($google->client->getAccessToken()){

            if($google->client->isAccessTokenExpired()){

                $google->client->authenticate($code);
                $NewAccessToken = json_decode($google->client->getAccessToken());
                $google->client->refreshToken($NewAccessToken->refresh_token);


            }
            $result = $google->getUser();
            $data = array(
                    'service_id'    => intval($result->id),
                    'name'          => 'google',
                    'first_name'    => $result->givenName,
                    'last_name'     => $result->familyName,
                    'email'         => $result->email,
                    'avatar'        => $result->picture,
            );

            Session::put('service_info', $data);
            $redirect = Session::pull('service_return_url');
            return Redirect::to((string)$redirect);

        }
        else{
            return $google->getAuthURL();
        }

    }


    public function instagramLogin()
    {

        $instagram = new Instagram;

        if(Input::has('code')){

            $instagram->storeSession();
            $user = $instagram->getUser();
            return $user;
        }
        else{
            return $instagram->getRedirectURL();
        }
    }


   /**
    *   Use a registered service to auth the
    *   registered user
    */
    public function loginWithService($service_id)
    {
        $service = Service::where('service_id', $service_id)->first();


        if($service){
            $user = $service->user()->first();
            return $this->login(array('email'=> $user->email, 'password' => $service_id));
        }


        return false;
    }


   /**
    *   Save service associated with the
    *   registered user
    */
    public function registerService($id, $name)
    {
        $service = new Service;
        $service->service_id = $id;
        $service->name = $name;

        $user = User::find(Auth::user()->id);

        $user->services()->save($service);

        return true;
    }

}
