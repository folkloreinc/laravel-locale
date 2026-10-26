# Laravel Locale

[![Latest Stable Version](https://poser.pugx.org/folklore/laravel-locale/v/stable.svg)](https://packagist.org/packages/folklore/laravel-locale)
[![Tests](https://github.com/folkloreinc/laravel-locale/actions/workflows/run-tests.yml/badge.svg?branch=8.x)](https://github.com/folkloreinc/laravel-locale/actions/workflows/run-tests.yml?query=branch%3A8.x)
[![Total Downloads](https://poser.pugx.org/folklore/laravel-locale/downloads.svg)](https://packagist.org/packages/folklore/laravel-locale)

Locale detection, localized routes and localized URLs for Laravel.

- Detects the locale of each request: request parameter, session, user preference, `Accept-Language` header, then a fallback.
- Sets the locale of the routes that declare one, and groups routes by locale (`/en/…`, `/fr/…`).
- Translates route URIs and generates the URL of a route in a given locale.
- Shares the locale with the views and sets Carbon's locale.

## Requirements

| Version | PHP           | Laravel | Branch |
| ------- | ------------- | ------- | ------ |
| 8.x     | 7.3+ or 8.0.2+ | 8 to 13 | `8.x`  |

CI tests PHP 8.2 to 8.5 with Laravel 10 to 13, the versions active projects use. Earlier versions (2.x, 3.x) are no longer maintained.

## Installation

```bash
composer require folklore/laravel-locale:^8.2
```

The service provider is registered by package discovery. Publish the configuration to `config/locale.php`:

```bash
php artisan vendor:publish --provider="Folklore\Locale\LocaleServiceProvider" --tag=config
```

## Configuration

| Key                           | Default        | Description                                                                                                                            |
| ----------------------------- | -------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| `locales`                     | `['en', 'fr']` | The supported locales.                                                                                                                 |
| `uses_user_locale_preference` | `true`         | Use the locale of the authenticated user when it implements `Illuminate\Contracts\Translation\HasLocalePreference`.                    |
| `request_parameter`           | `null`         | The name of a request parameter (query string or input) that sets the locale, such as `'locale'`.                                     |
| `fallback_to_app_locale`      | `false`        | When no locale is detected, fall back to `app.locale` if it is one of `locales`, instead of the first entry of `locales`.              |
| `detect_from_route`           | `true`         | Set the locale of a matched route that declares one.                                                                                   |
| `detect_from_headers`         | `true`         | Detect the locale from the `Accept-Language` header.                                                                                   |
| `store_in_session`            | `true`         | Read the locale from the session, and store the detected locale in it, when the request has a session.                                 |
| `share_with_views`            | `true`         | Share the current locale with every view as `$locale`.                                                                                 |
| `set_carbon_locale`           | `true`         | Keep Carbon's locale in sync with the application locale.                                                                              |
| `translations_namespace`      | `'routes'`     | The translation file of the localized route URIs (`lang/{locale}/routes.php`).                                                         |

Laravel merges package configuration at the first level only: a published `config/locale.php` replaces each key it defines.

## Locale detection

The package adds `Folklore\Locale\LocaleMiddleware` to the global middleware. For each request, it takes the first supported locale among:

1. the request parameter (`request_parameter`);
2. the session (`store_in_session`);
3. the user's preference (`uses_user_locale_preference`);
4. the `Accept-Language` header (`detect_from_headers`).

When none of them gives a supported locale, it falls back to the first entry of `locales`, or to `app.locale` when `fallback_to_app_locale` is enabled and `app.locale` is supported. Enable it so the order of `locales` doesn't decide the default language:

```php
// config/locale.php
'fallback_to_app_locale' => true,
```

It is off by default in 8.x to keep the previous behaviour ([decisions/0002](docs/decisions/0002-fallback-to-app-locale-opt-in.md)).

Then, when a route that declares a supported locale is matched, that locale wins (`detect_from_route`).

The locales the middleware detects can depend on the request, for example on the domain:

```php
use Folklore\Locale\LocaleManager;

app(LocaleManager::class)->setLocalesResolver(function ($request, array $locales) {
    return $request->getHost() === 'example.fr' ? ['fr'] : $locales;
});
```

The current locale is also available as `$request->locale()`.

## Localized routes

### Set the locale of a route

```php
Route::get('/fr', function () {
    return view('home');
})->locale('fr');

Route::get('/en', ['locale' => 'en', function () {
    return view('home');
}]);
```

### Group routes by locale

`groupWithLocales()` registers its routes once per supported locale, prefixed with the locale and with the locale set:

```php
Route::groupWithLocales(function ($locale) {
    Route::get('/', HomeController::class)->nameWithLocale('home'); // en.home: /en, fr.home: /fr
});

Route::groupWithLocales(['prefix' => 'blog'], function ($locale) {
    Route::get('/', BlogController::class)->nameWithLocale('blog'); // en.blog: /en/blog, fr.blog: /fr/blog
});
```

### Translate route URIs

`getTrans()`, `postTrans()`, `putTrans()`, `patchTrans()`, `deleteTrans()` and `optionsTrans()` look up the URI in the `routes` translation file of the group's locale, and keep it as is when there is no translation. `resourceTrans()` does the same for a resource.

```php
// lang/fr/routes.php
return [
    'about' => 'a-propos',
];

// routes/web.php
Route::groupWithLocales(function ($locale) {
    Route::getTrans('about', AboutController::class)->nameWithLocale('about'); // /en/about, /fr/a-propos
});
```

### Generate localized URLs

Localized route names are prefixed with their locale (`fr.about`). The helpers use the current locale when none is given:

```php
route_with_locale('about');        // the URL of the current locale's route
route_with_locale('about', 'fr');  // http://example.com/fr/a-propos
url()->routeWithLocale('about', 'fr', [], false); // /fr/a-propos

route_name_with_locale('about', 'fr'); // fr.about
Route::nameWithLocale('about');        // the name in the current locale
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Changes are listed in the [CHANGELOG](CHANGELOG.md), and the plan for the package is in [docs/roadmap.md](docs/roadmap.md).

## License

MIT. See [LICENSE.md](LICENSE.md).
