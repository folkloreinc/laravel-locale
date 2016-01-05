<?php namespace Folklore\LaravelLocale;

use App;
use View;
use session;

class LocaleEventHandler {

    /**
     * Handle user login events.
     */
    public function onRouteMatched($route, $request)
    {
        $currentLocale = config('app.locale');
        $action = $route->getAction();
        $locales = config('locale.locales');
        if(isset($action['locale']) && $action['locale'] !== $currentLocale && in_array($action['locale'], $locales))
		{
			App::setLocale($action['locale']);
		}
    }
    
    public function onLocaleChanged($currentLocale)
    {
        if(config('locale.store_in_session'))
        {
            Session::put('locale', $currentLocale);
        }
        
        $otherLocales = array();
        $locales = config('locale.locales');
        foreach($locales as $locale)
        {
            if($locale !== $currentLocale)
            {
                $otherLocales[] = $locale;
            }
        }
        
        if(config('locale.share_with_views', true))
        {
            View::share('locale', $currentLocale);
            View::share('otherLocales', $otherLocales);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('Illuminate\Routing\Events\RouteMatched', 'Folklore\LaravelLocale\LocaleEventHandler@onRouteMatched');
        $events->listen('locale.changed', 'Folklore\LaravelLocale\LocaleEventHandler@onLocaleChanged');
    }

}
