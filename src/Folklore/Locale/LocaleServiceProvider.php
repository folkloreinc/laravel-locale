<?php namespace Folklore\Locale;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Http\Request;
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
        $this->bootRouting();
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

        Request::macro('locale', function () {
            return app()->getLocale();
        });
    }

    public function bootViews()
    {
        if (!$this->app['config']->get('locale.share_with_views', false)) {
            return;
        }

        $this->app['view']->share('locale', $this->app->getLocale());
        $this->app['events']->listen(Illuminate\Foundation\Events\LocaleUpdated::class, function (
            $locale
        ) {
            $this->app['view']->share('locale', $this->app->getLocale());
        });
    }

    public function bootMiddlewares()
    {
        $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(
            LocaleMiddleware::class
        );
    }

    public function bootRouting()
    {
        if (!$this->app['config']->get('locale.detect_from_route', true)) {
            return;
        }

        // Set locale when route is matched
        $locales = $this->app['config']->get('locale.locales');
        $this->app['events']->listen(RouteMatched::class, function (RouteMatched $event) use (
            $locales
        ) {
            $locale = $this->app->getLocale();
            $action = $event->route->getAction();
            // prettier-ignore
            if (isset($action['locale']) &&
                $action['locale'] !== $locale &&
                in_array($action['locale'], $locales)
            ) {
                $this->app->setLocale($action['locale']);
            }
        });
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
