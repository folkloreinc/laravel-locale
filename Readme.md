# Laravel Locale

A simple localization package for Laravel 5. It provides URL detection, Route parameters, session storage, and view sharing.

[![Latest Stable Version](https://poser.pugx.org/folklore/laravel-locale/v/stable.svg)](https://packagist.org/packages/folklore/laravel-locale)
[![Tests](https://github.com/folkloreinc/laravel-locale/actions/workflows/run-tests.yml/badge.svg?branch=8.x)](https://github.com/folkloreinc/laravel-locale/actions/workflows/run-tests.yml?query=branch%3A8.x)
[![Total Downloads](https://poser.pugx.org/folklore/laravel-locale/downloads.svg)](https://packagist.org/packages/folklore/laravel-locale)

## Installation

#### Dependencies:

##### Version prior to 3.0
* [Laravel <= 5.7](https://github.com/laravel/laravel)


##### Version 3.0 and up
* [Laravel >= 5.5](https://github.com/laravel/laravel)

#### Installation:

**1-** Require the package via Composer in your `composer.json`.
```json
{
	"require": {
		"folklore/laravel-locale": "~3.0"
	}
}
```

**2-** Run Composer to install or update the new requirement.

```bash
$ composer install
```

or

```bash
$ composer update
```

**3-** Add the service provider to your `app/config/app.php` file
```php
\Folklore\LaravelLocale\LocaleServiceProvider::class,
```

**5-** Publish the configuration file

```bash
$ php artisan vendor:publish --provider="Folklore\LaravelLocale\LocaleServiceProvider"
```

**6-** Review the configuration file

```
config/locale.php
```

## Usage

### Define route locale
You can specify the locale of a specific routes:
```php
Route::get('/fr', [
  'as' => 'home.fr',
  'locale' => 'fr',
  function()
  {
    return view('home');
  }
]);
```

### Auto-detection from URL
It is possible to let the package auto-detect the locale from the first part fo the URL.

```php
Route::get('/fr/a-propos', [
  'as' => 'about.fr',
  function()
  {
    return view('home');
  }
]);
```

Be sure to add the supported locales in `config/locale.php`

### View sharing
By default, this package shares two variables to every views `$locale` and `$otherLocales`. The `$otherLocales` variable is an array containing all the other locales than the current one.

### Fallback locale
When no locale is detected (from the request parameter, the session, the user's preference or the `Accept-Language` header), the middleware uses the first entry of `locales`. To use the application's default locale (`app.locale`) instead, when it is one of `locales`, enable this option in `config/locale.php`:

```php
'fallback_to_app_locale' => true,
```

It is off by default in 8.x, to keep the previous behaviour.
