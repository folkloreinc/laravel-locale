# Laravel Locale

A simple localization package for Laravel 5. It provides URL detection, Route parameters, session storage, and view sharing.

[![Latest Stable Version](https://poser.pugx.org/folklore/locale/v/stable.svg)](https://packagist.org/packages/folklore/locale)
[![Build Status](https://travis-ci.org/Folkloreatelier/laravel-locale.png?branch=master)](https://travis-ci.org/Folkloreatelier/laravel-locale)
[![Total Downloads](https://poser.pugx.org/folklore/locale/downloads.svg)](https://packagist.org/packages/folklore/locale)

## Installation

#### Dependencies:

* [Laravel 5.x](https://github.com/laravel/laravel)


#### Installation:

**1-** Require the package via Composer in your `composer.json`.
```json
{
	"require": {
		"folklore/locale": "~1.0"
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
'Folklore\LaravelLocale\LocaleServiceProvider',
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
