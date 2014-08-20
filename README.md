Identichip
==========

A Laravel Package for login/registration. Using email and other services.


##### Status

This package is under development



##### Preinstalled
This package uses a OAuth wrapper for laravel 4 from Artdarek:
https://github.com/artdarek/oauth-4-laravel


##### Setup


1. Inside your Laravel root  create the following structure :

`workbench/nobox/identichip/`

2. Clone this repo inside the the folder identichip you just created.
3. Change the scope to the identichip folder, and run `composer install`
4. Register this service provider to your lightsaber `app/config/app.php` to do this, just add to the providers array this line: `Nobox\Identichip\IdentichipServiceProvider`
5. Run migrations :    `php artisan migrate --bench="nobox/identichip"`
