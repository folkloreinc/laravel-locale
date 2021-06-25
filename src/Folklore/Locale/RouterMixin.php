<?php

namespace Folklore\Locale;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;

class RouterMixin
{
    protected $app;

    protected $translator;

    protected $locales;

    public function __construct(Application $app, Translator $translator)
    {
        $this->app = $app;
        $this->translator = $translator;
        $this->locales = $this->app['config']->get(
            'locale.locales',
            $this->app['config']->get('app.locales', [$this->app->getLocale()])
        );
    }

    public function nameWithLocale()
    {
        $app = $this->app;
        return function ($name, $locale = null) use ($app) {
            if (is_null($locale)) {
                $locale = $app->getLocale();
            }
            return $locale . '.' . $name;
        };
    }

    public function locale()
    {
        return function ($locale) {
            $this->mergeWithLastGroup([
                'locale' => $locale,
            ]);
            return $this;
        };
    }

    public function groupWithLocales()
    {
        $locales = $this->locales;
        return function ($callback, $action = []) use ($locales) {
            $originalAction = $action;
            $action = is_array($callback) ? $callback : $action;
            $callback = is_array($callback) ? $originalAction : $callback;
            $prefix = isset($action['prefix']) ? '/' . $action['prefix'] : '';
            foreach ($locales as $locale) {
                $groupAction = array_merge($action, [
                    'prefix' => $locale . $prefix,
                    'locale' => $locale,
                ]);
                $this->group($groupAction, function () use ($locale, $callback) {
                    $callback($locale);
                });
            }
            return $this;
        };
    }

    public function addRouteTrans()
    {
        $app = $this->app;
        $translator = $this->translator;

        return function ($methods, $uri, $action = null, $locale = null) use ($app, $translator) {
            if (is_null($locale)) {
                if ($this->hasGroupStack()) {
                    $groupStack = $this->getGroupStack();
                    $locale = data_get(end($groupStack), 'locale', $app->getLocale());
                } else {
                    $locale = $app->getLocale();
                }
            }
            $uri = $translator->has(
                config('locale.translations_namespace', 'routes') . '.' . $uri,
                $locale
            )
                ? $translator->get(
                    config('locale.translations_namespace', 'routes') . '.' . $uri,
                    [],
                    $locale
                )
                : $uri;
            return $this->addRoute((array) $methods, $uri, $action);
        };
    }

    public function getTrans()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteTrans(['GET', 'HEAD'], $uri, $action, $locale);
        };
    }

    public function postTrans()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteTrans('POST', $uri, $action, $locale);
        };
    }

    public function putTrans()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteTrans('PUT', $uri, $action, $locale);
        };
    }

    public function patchTrans()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteTrans('PATCH', $uri, $action, $locale);
        };
    }

    public function deleteTrans()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteTrans('DELETE', $uri, $action, $locale);
        };
    }

    public function optionsTrans()
    {
        return function ($uri, $action = null, $locale = null) {
            return $this->addRouteTrans('OPTIONS', $uri, $action, $locale);
        };
    }

    public function resourceTrans()
    {
        $app = $this->app;
        $translator = $this->translator;

        return function ($name, $controller, $locale = null) use ($app, $translator) {
            if (is_null($locale)) {
                if ($this->hasGroupStack()) {
                    $groupStack = $this->getGroupStack();
                    $locale = data_get(end($groupStack), 'locale', $app->getLocale());
                } else {
                    $locale = $app->getLocale();
                }
            }
            $localizedName = $translator->has(
                config('locale.translations_namespace', 'routes') . '.' . $name,
                $locale
            )
                ? $translator->get(
                    config('locale.translations_namespace', 'routes') . '.' . $name,
                    [],
                    $locale
                )
                : $name;
            $names = $this->nameWithLocale($name, $locale);
            return $this->resource($localizedName, $controller)
                ->names($names)
                ->parameters([
                    $localizedName => $name,
                ]);
        };
    }
}
