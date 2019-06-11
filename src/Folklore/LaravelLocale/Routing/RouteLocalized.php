<?php

namespace Folklore\LaravelLocale\Routing;

class RouteLocalized
{
    public function nameWithLocale()
    {
        return function ($name, $locale = null) {
            return app('router')->nameLocalized($name, $locale);
        };
    }
}
