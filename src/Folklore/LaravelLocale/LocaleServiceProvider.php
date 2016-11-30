<?php namespace Folklore\LaravelLocale;

use Illuminate\Support\ServiceProvider;

class LocaleServiceProvider extends ServiceProvider
{

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
        $this->bootPublishes();
        $this->bootMiddlewares();
        $this->bootEventSubscriber();
        $this->bootLocale();
    }
    
    protected function bootPublishes()
    {
        $configPath = __DIR__ . '/../../config/locale.php';
        
        $this->mergeConfigFrom($configPath, 'locale');
        
        $this->publishes([
            $configPath => config_path('locale.php')
        ], 'config');
    }
    
    public function bootMiddlewares()
    {
        $http = $this->app['Illuminate\Contracts\Http\Kernel'];
        $http->pushMiddleware('Folklore\LaravelLocale\LocaleMiddleware');
    }
    
    public function bootEventSubscriber()
    {
        $this->app['events']->subscribe('Folklore\LaravelLocale\LocaleEventHandler');
    }
    
    protected function bootLocale()
    {
        $this->app['locale.manager']->setLocale($this->app->getLocale());
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLocaleManager();
    }
    
    public function registerLocaleManager()
    {
        $this->app->singleton('locale.manager', function ($app) {
            return new LocaleManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('locale.manager');
    }
}
