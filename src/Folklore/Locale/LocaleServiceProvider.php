<?php

namespace Folklore\Locale;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
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
     * Register
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('locale.manager', function ($app) {
            return new LocaleManager($app);
        });

        $this->app->alias('locale.manager', LocaleManager::class);

        $this->app->bind(RouterMixin::class, function ($app) {
            return new RouterMixin(
                $app['locale.manager'],
                $app['translator'],
                $app['config']->get('locale.translations_namespace', 'routes')
            );
        });
    }

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
        $this->bootCarbon();
    }

    protected function bootPublishes()
    {
        $configPath = __DIR__.'/../../config/locale.php';

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
        $this->attachMixin(Router::class, RouterMixin::class);
        $this->attachMixin(Route::class, RouteMixin::class);
        $this->attachMixin(UrlGenerator::class, UrlGeneratorMixin::class);

        Request::macro('locale', function () {
            return app()->getLocale();
        });
    }

    public function bootCarbon()
    {
        if (! $this->app['config']->get('locale.set_carbon_locale', true)) {
            return;
        }

        Carbon::setLocale($this->app->getLocale());
        $this->app['events']->listen(LocaleUpdated::class, function (
            $locale
        ) {
            Carbon::setLocale($this->app->getLocale());
        });
    }

    public function bootViews()
    {
        if (! $this->app['config']->get('locale.share_with_views', true)) {
            return;
        }

        $this->app['view']->share('locale', $this->app->getLocale());
        $this->app['events']->listen(LocaleUpdated::class, function (
            $locale
        ) {
            $this->app['view']->share('locale', $this->app->getLocale());
        });
    }

    public function bootMiddlewares()
    {
        $this->app[Kernel::class]->pushMiddleware(
            LocaleMiddleware::class
        );
    }

    public function bootRouting()
    {
        if (! $this->app['config']->get('locale.detect_from_route', true)) {
            return;
        }

        // Set locale when route is matched
        $this->app['events']->listen(RouteMatched::class, function (RouteMatched $event) {
            $locales = config('locale.locales', config('app.locales', [app()->getLocale()]));
            if ($locales instanceof Closure) {
                $locales = $locales($event->request);
            }
            $locale = $this->app->getLocale();
            $action = $event->route->getAction();
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
