<?php
namespace Nobox\Identichip\Models;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends \Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

        public static $rules = array(
        'first_name'    => 'Required|Alpha',
        'last_name'     => 'Required',
        'email'         => 'Required|Between:3,64|Email|Unique:users',
    );


    /*Validates user registration form */
    public function isValid()
    {

        $validation = \Validator::make($this->attributes, static::$rules);

        if($validation->passes()) return true;

        $this->errors  = $validation->messages()->toArray();
        return false;
    }

    /**
     * user one to many relationship with services.
     * one user have many services
     */
    public function services()
    {
        return $this->hasMany('Service');
    }

    /**
     * user one to one relationship with extended_info.
     * one user have one extended_info
     * this table is dynamicaly generated based on
     * config file
     */
    public function extended_info()
    {
        return $this->hasOne('Extended_info');
    }
}
