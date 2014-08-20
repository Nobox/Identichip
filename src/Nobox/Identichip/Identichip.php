<?php

namespace Nobox\Identichip;

use \Config;
use \Input;
use \Auth;
use \Hash;
use \Redirect;
use \Session;

use Artdarek\OAuth\OAuth;
use User;
use Nobox\Identichip\Models\Service as Service;

class Identichip{

    


   /**
    * Perform user registration into DB
    * @param array $newUser from user registration form
    * @return mixed values : true if all good | object errors
    */
    public static function register($newUser)
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
            return $user->errors;


        $user->save();

        Auth::login($user);

        return true;
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

   /**
    *   Facebook Login/Registration Implementation
    *   this uses Oauth Library
    */
    public function facebookLogin($redirect = '')
    {

        $OAuth = new OAuth;

        // get data from input
        $code = Input::get( 'code' );
        // get fb service
        $fb =  $OAuth->consumer('Facebook');
        // check if code is valid
        $service = array();

        // if code is provided get user data and sign in
        if ( !empty( $code ) || !is_null($code)) {

            $token = $fb->requestAccessToken( $code );
            $result = json_decode( $fb->request( '/me' ), true );

            $data = array(
                    'service_id'    => $result['id'],
                    'name'          => 'facebook',
                    'first_name'    => $result['first_name'],
                    'last_name'     => $result['last_name'],
                    'email'         => $result['email'],
            );
            Session::put('service_info', $data);
            return Redirect::to($redirect);
        }
        // if not ask for permission first
        else {
            // get fb authorization
            $url = $fb->getAuthorizationUri();
            // return to facebook login url
            return Redirect::to( (string)$url );
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
