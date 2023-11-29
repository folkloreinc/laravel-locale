<?php

namespace Folklore\Locale;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;

class RouterMixin
{
    protected $manager;

    protected $translator;

    protected $translationsNamespace;

    public function __construct(
        LocaleManager $manager,
        Translator $translator,
        string $translationsNamespace = 'routes'
    ) {
        $this->manager = $manager;
        $this->translator = $translator;
        $this->translationsNamespace = $translationsNamespace;
    }

    public function nameWithLocale()
    {
        $manager = $this->manager;
        return function ($name, $locale = null) use ($manager) {
            if (is_null($locale)) {
                $locale = $manager->locale();
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
        $locales = $this->manager->locales();
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
        $manager = $this->manager;
        $translator = $this->translator;
        $namespace = $this->translationsNamespace;

        return function ($methods, $uri, $action = null, $locale = null) use (
            $manager,
            $translator,
            $namespace
        ) {
            if (is_null($locale)) {
                if ($this->hasGroupStack()) {
                    $groupStack = $this->getGroupStack();
                    $locale = data_get(end($groupStack), 'locale', $manager->locale());
                } else {
                    $locale = $manager->locale();
                }
            }
            $uri = $translator->has($namespace . '.' . $uri, $locale)
                ? $translator->get($namespace . '.' . $uri, [], $locale)
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
        $manager = $this->manager;
        $translator = $this->translator;
        $namespace = $this->translationsNamespace;

        return function ($name, $controller, $locale = null) use (
            $manager,
            $translator,
            $namespace
        ) {
            if (is_null($locale)) {
                if ($this->hasGroupStack()) {
                    $groupStack = $this->getGroupStack();
                    $locale = data_get(end($groupStack), 'locale', $manager->locale());
                } else {
                    $locale = $manager->locale();
                }
            }
            $localizedName = $translator->has($namespace . '.' . $name, $locale)
                ? $translator->get($namespace . '.' . $name, [], $locale)
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
