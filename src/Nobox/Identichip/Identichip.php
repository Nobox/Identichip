<?php

namespace Nobox\Identichip;

use \Config;
use \Input;


class Identichip{

    /**
     * Perform user registration into DB
     * @param array $newUser from user registration form
     * @return mixed values : true if all good | object errors
     */
    public static function register($newUser)
    {
        $user = User();
        $user->email = $newUser['email'];
        $user->first_name = $newUser['first_name'];
        $user->last_name = $newUser['last_name'];

        //check if password is null
        //password is not needed for some
        if(isset($newUser['password'])){
            $user->password = \Hash::make($newUser['password']);
        }

        if(!$user->isValid())
            return \Redirect::back()->withInput()->withErrors($user->errors);


        $user->save();

        return true;
    }

    /*Facebook Login/Registration Implementation
    /*this uses Oauth Library
    */

    public function loginWithFacebook() {

        // get data from input
        $code = \Input::get( 'code' );
        // get fb service
        $fb = \OAuth::consumer( 'Facebook' );
        // check if code is valid

        // if code is provided get user data and sign in
        if ( !empty( $code ) || !is_null($code)) {
            // This was a callback request from facebook, get the token
            $token = $fb->requestAccessToken( $code );

            // Send a request with it
            $result = json_decode( $fb->request( '/me' ), true );

            $message = 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
            echo $message. "<br/>";

            //Var_dump
            //display whole array().
            dd($result);

        }
        // if not ask for permission first
        else {
            // get fb authorization
            $url = $fb->getAuthorizationUri();

            // return to facebook login url
            return \Redirect::to( (string)$url );
        }

    }

    /* I need to add user registered by service, to the DB, */
    // I need to search user in the DB to login

}
