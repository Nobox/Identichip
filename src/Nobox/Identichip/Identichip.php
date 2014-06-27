<?php namespace Nobox\Identichip;


class Identichip{

    public static function holoGreetings()
    {
        return \Response::json(\Config::get('identichip::oauth-4-laravel'));
        // return "May the force be with you";
    }

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
}