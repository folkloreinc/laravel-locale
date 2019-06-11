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
        $this->bootMacros();
        $this->bootMiddlewares();
        $this->bootEventSubscriber();
        $this->bootLocale();
    }

    protected function bootPublishes()
    {
        $configPath = __DIR__ . '/../../config/locale.php';

        $this->mergeConfigFrom($configPath, 'locale');

        $this->publishes(
            [
                $configPath => config_path('locale.php')
            ],
            'config'
        );
    }

    public function bootMacros()
    {
        \Illuminate\Routing\Router::mixin(
            $this->app->make(
                \Folklore\LaravelLocale\Routing\RouterLocalized::class
            )
        );
        \Illuminate\Routing\Route::mixin(
            $this->app->make(
                \Folklore\LaravelLocale\Routing\RouteLocalized::class
            )
        );
        \Illuminate\Routing\UrlGenerator::mixin(
            $this->app->make(
                \Folklore\LaravelLocale\Routing\UrlGeneratorLocalized::class
            )
        );
    }

    public function bootMiddlewares()
    {
        $http = $this->app['Illuminate\Contracts\Http\Kernel'];
        $http->pushMiddleware('Folklore\LaravelLocale\LocaleMiddleware');
    }

    public function bootEventSubscriber()
    {
        $this->app['events']->subscribe(
            'Folklore\LaravelLocale\LocaleEventHandler'
        );
    }

    public function bootLocale()
    {
        $this->app->booted(function ($app) {
            $app['locale.manager']->setLocale($app->getLocale(), false);
        });
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
