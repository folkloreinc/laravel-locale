<?php namespace Folklore\LaravelLocale;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Foundation\Events\LocaleUpdated;

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

    public function onLocaleUpdated(LocaleUpdated $event)
    {
        app('locale.manager')->setLocale($event->locale);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('locale.changed', self::class.'@onLocaleChanged');
        $events->listen(LocaleUpdated::class, self::class.'@onLocaleUpdated');
        $events->listen(RouteMatched::class, self::class.'@onRouteMatched');
    }
}
