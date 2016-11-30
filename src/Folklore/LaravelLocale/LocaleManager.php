<?php namespace Folklore\LaravelLocale;

use App;
use Session;

class LocaleManager
{
    protected $app;
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function setLocale($locale)
    {
        if ($locale !== $this->app->getLocale()) {
            $this->app->setLocale($locale);
            return;
        }
        
        if ($this->app['config']->get('locale.store_in_session')) {
            $this->app['session']->put('locale', $locale);
        }
        
        if ($this->app['config']->get('locale.share_with_views', true)) {
            $otherLocales = $this->getOtherLocales($locale);
            $this->app['view']->share('locale', $locale);
            $this->app['view']->share('otherLocales', $otherLocales);
        }
    }
    
    public function getLocale()
    {
        return $this->app->getLocale();
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
