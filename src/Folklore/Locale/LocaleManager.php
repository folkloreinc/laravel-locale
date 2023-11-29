<?php

namespace Folklore\Locale;

use Closure;

class LocaleManager
{
    protected $app;

    protected $localesResolver;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function locale()
    {
        return $this->app->getLocale();
    }

    public function locales()
    {
        return $this->app['config']->get(
            'locale.locales',
            $this->app['config']->get('app.locales', [$this->app->getLocale()])
        );
    }

    public function resolveLocalesFromRequest($request)
    {
        $locales = $this->locales();
        $localesResolver = $this->localesResolver;
        return isset($localesResolver) ? $localesResolver($request, $locales) : $locales;
    }

    public function setLocalesResolver(?Closure $resolver)
    {
        $this->localesResolver = $resolver;
        return $this;
    }
}
