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
    public function facebookLogin($redirect)
    {
        $current_url = Request::url();
        $facebook = new Facebook;

        $session = $facebook->facebokLoginHelper($current_url);

        if ($session) {
            $result = $facebook->doFacebookRequest($session, 'GET', '/me');
            $data = array(
                    'service_id'    => $result->getId(),
                    'name'          => 'facebook',
                    'first_name'    => $result->getFirstName(),
                    'last_name'     => $result->getLastName(),
                    'email'         => $result->getProperty('email'),
            );

            Session::put('service_info', $data);
            return Redirect::to($redirect);
        }
        else{
            return $facebook->getAuthURL($current_url);
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
