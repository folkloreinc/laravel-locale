<?php namespace Folklore\LaravelLocale;

use App;
use View;
use Session;

use Illuminate\Routing\Events\RouteMatched;

class LocaleEventHandler
{

    /**
     * Handle user login events.
     */
    public function onRouteMatched(RouteMatched $event)
    {
        $app = app();
        $route = $event->route;
        $currentLocale = $app->getLocale();
        $action = $route->getAction();
        $locales = config('locale.locales');
        if (isset($action['locale']) && $action['locale'] !== $currentLocale && in_array($action['locale'], $locales)) {
            $app->setLocale($action['locale']);
        }
    }
    
    public function onLocaleChanged($currentLocale)
    {
        app('locale.manager')->setLocale($currentLocale);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen(
            \Illuminate\Routing\Events\RouteMatched::class,
            self::class.'@onRouteMatched'
        );
        $events->listen(
            'locale.changed',
            self::class.'@onLocaleChanged'
        );
    }
}
