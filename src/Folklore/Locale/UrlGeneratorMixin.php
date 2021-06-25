<?php

namespace Folklore\Locale\Routing;

use Illuminate\Routing\Router;

class UrlGeneratorMixin
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function routeWithLocale()
    {
        $router = $this->router;
        return function ($name, $locale = null, $parameters = [], $absolute = true) use ($router) {
            if (is_array($locale)) {
                $parameters = $locale;
                $absolute = !is_array($parameters) ? $parameters : $absolute;
                $locale = null;
            }
            $nameWithLocale = $router->nameWithLocale($name, $locale);
            return $this->route($nameWithLocale, $parameters, $absolute);
        };
    }
}
