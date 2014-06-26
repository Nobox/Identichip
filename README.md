Identichip
==========

A Laravel Package for login/registration. Using email and other services. 


##### Setup

(Install a git-less lightsaber in your homestead, mine is called lightsaber.app)

1. Inside your Lightsaber root  create the following structure : 

`workbench/nobox/identichip/`

2. Clone this repo inside the the folder identichip you just created. 
3. Change the scope to the identichip folder, and run `composer install`
4. Register this service provider to your lightsaber `app/config/app.php` to do this, just add to the providers array this line: `Nobox\Identichip\IdentichipServiceProvider`

Now you are good to go, and use/edit the class Identichip : 

`src/Nobox/Identichip/Identichip.php`
