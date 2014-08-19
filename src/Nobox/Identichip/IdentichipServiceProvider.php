<?php namespace Nobox\Identichip;

use Illuminate\Support\ServiceProvider;

class IdentichipServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('nobox/identichip');
        include __DIR__.'/../../routes.php';

        //loads artdarek oauth config into app config
        $this->app['config']['oauth-4-laravel'] = \Config::get('identichip::oauth-4-laravel');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['identichip'] = $this->app->share(function($app)
        {
            return new Identichip;
        });


        $this->app->booting(function()
        {
          $loader = \Illuminate\Foundation\AliasLoader::getInstance();
          $loader->alias('Identichip', 'Nobox\Identichip\Facades\Identichip');
          $loader->alias('OAuth','Artdarek\OAuth\Facade\OAuth');
          $loader->alias('User','Nobox\Identichip\Models\User');
          $loader->alias('Service', 'Nobox\Identichip\Models\Service');
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('identichip');
    }

}
