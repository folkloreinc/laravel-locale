<?php namespace Folklore\LaravelLocale;

use Folklore\LaravelLocale\LocaleChanged;

class LocaleManager
{
    protected $app;
    protected $locale;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function setLocale($locale, $storeInSession = true)
    {
        if ($locale !== $this->app->getLocale()) {
            $this->app->setLocale($locale);
            return;
        }

        if ($this->locale === $locale) {
            return;
        }

        $this->locale = $locale;

        if ($storeInSession && $this->app['config']->get('locale.store_in_session')) {
            $this->app['session']->put('locale', $locale);
        }

        if ($this->app['config']->get('locale.share_with_views', true)) {
            $otherLocales = $this->getOtherLocales($locale);
            $this->app['view']->share('locale', $locale);
            $this->app['view']->share('otherLocales', $otherLocales);
        }

        $events = $this->app['events'];
        if (method_exists($events, 'fire')) {
            $events->fire(new LocaleChanged($locale));
        } else {
            $events->dispatch(new LocaleChanged($locale));
        }
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getOtherLocales($currentLocale = null)
    {
        if ($currentLocale === null) {
            $currentLocale = $this->app->getLocale();
        }

        $otherLocales = array();
        $locales = $this->app['config']->get('locale.locales');
        foreach ($locales as $locale) {
            if ($locale !== $currentLocale) {
                $otherLocales[] = $locale;
            }
        }

        return $otherLocales;
    }
}
