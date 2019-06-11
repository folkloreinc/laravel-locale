<?php

namespace Folklore\LaravelLocale\Routing;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

class RouterLocalized
{
    public function groupWithLocales()
    {
        return function ($callback, $action = []) {
            $locales = config('locale.locales', [app()->getLocale()]);
            foreach ($locales as $locale) {
                $this->prefix($locale)->group(
                    array_merge(
                        [
                            'locale' => $locale,
                        ],
                        is_array($callback) ? $callback : $action
                    ),
                    function () use ($locale, $callback, $action) {
                        if (is_array($callback)) {
                            $action($locale);
                        } else {
                            $callback($locale);
                        }
                    }
                );
            }
            return $this;
        };
    }

    public function addRouteLocalized()
    {
        return function ($methods, $uri, $action = null, $locale = null) {
            if (is_null($locale)) {
                $locale = $this->hasGroupStack()
                    ? array_get($this->getGroupStack(), 'locale', app()->getLocale())
                    : app()->getLocale();
            }
            $transNamespace = config('locale.routes_translations_namespace', 'routes');
            $transKey = !empty($transNamespace) ? sprintf('%s.%s', $transNamespace, $uri) : $uri;
            return $this->addRoute((array)$methods, trans($transKey, [], $locale), $action);
        };
    }

    public function getLocalized()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteLocalized(['GET', 'HEAD'], $uri, $action, $locale);
        };
    }

    public function postLocalized()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteLocalized('POST', $uri, $action, $locale);
        };
    }

    public function putLocalized()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteLocalized('PUT', $uri, $action, $locale);
        };
    }

    public function patchLocalized()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteLocalized('PATCH', $uri, $action, $locale);
        };
    }

    public function deleteLocalized()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteLocalized('DELETE', $uri, $action, $locale);
        };
    }

    public function optionsLocalized()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteLocalized('OPTIONS', $uri, $action, $locale);
        };
    }

    public function anyLocalized()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteLocalized(Router::$verbs, $uri, $action, $locale);
        };
    }

    public function prefixLocalized()
    {
        return function ($prefix, $locale = null) {
            if (is_null($locale)) {
                $locale = $this->hasGroupStack()
                    ? array_get($this->getGroupStack(), 'locale', app()->getLocale())
                    : app()->getLocale();
            }
            $transNamespace = config('locale.routes_translations_namespace', 'routes');
            $transKey = !empty($transNamespace) ? sprintf('%s.%s', $transNamespace, $prefix) : $prefix;
            return (new RouteRegistrar($this))->attribute('prefix', trans($transKey, [], $locale));
        };
    }

    public function nameLocalized()
    {
        return function ($name, $locale = null) {
            if (is_null($locale)) {
                $locale = app()->getLocale();
            }
            return $name.'.'.$locale;
        };
    }
}
