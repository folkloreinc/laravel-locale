<?php

namespace Folklore\LaravelLocale\Routing;

class RouteLocalized
{
    public function nameWithLocale()
    {
        return function ($name, $locale = null) {
            if (is_null($locale)) {
                $locale = $this->getAction('locale');
            }
            $nameWithLocale = app('router')->nameLocalized($name, $locale);
            return $this->name($nameWithLocale);
        };
    }
}
