<?php

if (! function_exists('route_with_locale')) {
    /**
     * Generate the URL to a named localized route.
     *
     * @param  string  $name
     * @param  string   $locale
     * @param  array   $parameters
     * @param  bool    $absolute
     * @return string
     */
    function route_with_locale($name, $locale = null, $parameters = [], $absolute = true)
    {
        if (is_array($locale)) {
            $parameters = $locale;
            $absolute = !is_array($parameters) ? $parameters : $absolute;
            $locale = null;
        }
        return app('url')->routeWithLocale($name, $locale, $parameters, $absolute);
    }
}

if (! function_exists('route_name_with_locale')) {
    /**
     * Get the name of a localized route
     *
     * @param  string  $name
     * @param  string   $locale
     * @return string
     */
    function route_name_with_locale($name, $locale = null)
    {
        return app('router')->nameWithLocale($name, $locale);
    }
}
