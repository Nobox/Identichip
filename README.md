Identichip
==========

A Laravel Package for login/registration. Using email and other services.


##### Status

This package is under development

#### Requirements
Laravel 4.2.*
php >=5.4.0

##### Preinstalled
    "facebook/php-sdk-v4" : "4.0.*",
    "kertz/twitteroauth": "dev-master",
    "google/apiclient": "1.0.*@beta"


##### Setup

Add package to your composer json
    "kertz/twitteroauth": "dev-master",
    "google/apiclient": "1.0.*@beta",
    "nobox/identichip" : "1.0.1"

``because twitter and google packages are not stable you have to include it
in your composer json. ``

Run composer install

Run package migrations before running your own 
( this create the Service and User tables)
    php artisan migrate --package=nobox/identichip

Add Service Provider to your app.php config:

``` 'Nobox\Identichip\IdentichipServiceProvider', ```

More to be specified.
