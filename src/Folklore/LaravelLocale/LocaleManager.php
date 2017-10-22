<?php namespace Folklore\LaravelLocale;

use Folklore\LaravelLocale\LocaleChanged;

class LocaleManager
{
    protected $app;
    protected $locale;

    public function __construct($app)
    {
        $this->app = $app;
        $this->setLocale($this->app->getLocale());
    }

    public function setLocale($locale)
    {
        if ($locale !== $this->app->getLocale()) {
            $this->app->setLocale($locale);
            return;
        }

        if ($this->locale === $locale) {
            return;
        }

        $this->locale = $locale;

        if ($this->app['config']->get('locale.store_in_session')) {
            $this->app['session']->put('locale', $locale);
        }

        if ($this->app['config']->get('locale.share_with_views', true)) {
            $otherLocales = $this->getOtherLocales($locale);
            $this->app['view']->share('locale', $locale);
            $this->app['view']->share('otherLocales', $otherLocales);
        }

        $this->app['events']->fire(new LocaleChanged($locale));
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

    public function route($name, $locale = null, $parameters = [], $absolute = true)
    {
        if (is_array($locale)) {
            $parameters = $locale;
            $absolute = !is_array($parameters) ? $parameters : $absolute;
            $locale = null;
        }
        $localizedName = $this->routeName($name, $locale);
        return route($localizedName, $parameters, $absolute);
    }

    public function routeName($name, $locale = null)
    {
        if (is_null($locale)) {
            $locale = $this->getLocale();
        }
        return $name.'.'.$locale;
    }
}
