<?php

namespace Folklore\LaravelLocale\Routing;

class UrlGeneratorLocalized
{
    public function routeLocalized()
    {
        return function ($name, $locale = null, $parameters = [], $absolute = true) {
            if (is_array($locale)) {
                $parameters = $locale;
                $absolute = !is_array($parameters) ? $parameters : $absolute;
                $locale = null;
            }
            $localizedName = app('router')->nameLocalized($name, $locale);
            return $this->route($localizedName, $parameters, $absolute);
        };
    }
}
