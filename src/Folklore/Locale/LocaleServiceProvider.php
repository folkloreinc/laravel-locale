<?php namespace Folklore\Locale;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use ReflectionMethod;

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
        $this->bootViews();
    }

    protected function bootPublishes()
    {
        $configPath = __DIR__ . '/../../config/locale.php';

        $this->mergeConfigFrom($configPath, 'locale');

        $this->publishes(
            [
                $configPath => config_path('locale.php'),
            ],
            'config'
        );
    }

    public function bootMacros()
    {
        $this->attachMixin(\Illuminate\Routing\Router::class, RouterMixin::class);
        $this->attachMixin(\Illuminate\Routing\Route::class, RouteMixin::class);
        $this->attachMixin(\Illuminate\Routing\UrlGenerator::class, UrlGeneratorMixin::class);
    }

    public function bootViews()
    {
        $this->app['events']->listen(Illuminate\Foundation\Events\LocaleUpdated::class, function (
            $locale
        ) {
            if (config('locale.share_with_views')) {
                $this->app['view']->share('locale', $this->app->getLocale());
            }
        });
    }

    public function bootMiddlewares()
    {
        $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(
            LocaleMiddleware::class
        );
    }

    protected function attachMixin($macroable, $mixin)
    {
        $mixin = is_string($mixin) ? $this->app->make($mixin) : $mixin;
        $methods = (new ReflectionClass($mixin))->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $name = $method->name;
            if ($name !== '__construct') {
                $macroable::macro($name, $mixin->{$name}());
            }
        }
    }
}
