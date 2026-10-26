<?php

namespace Folklore\Locale\Tests\Feature;

use Folklore\Locale\Tests\TestCase;

class LocaleMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->get('/', function () {
            return app()->getLocale();
        });
    }

    protected function getLocale(?string $acceptLanguage = null): string
    {
        // Symfony sends `Accept-Language: en-us,en;q=0.5` by default; an empty header
        // means nothing is detected from the headers.
        return $this->withHeader('Accept-Language', $acceptLanguage ?? '')
            ->get('/')
            ->assertOk()
            ->getContent();
    }

    public function testFallsBackToTheFirstLocaleByDefault()
    {
        $this->app->setLocale('fr');

        $this->assertSame('en', $this->getLocale());
    }

    public function testFallsBackToTheAppLocaleWhenEnabled()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('fr');

        $this->assertSame('fr', $this->getLocale());
    }

    public function testFallsBackToTheFirstLocaleWhenTheAppLocaleIsNotSupported()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('de');

        $this->assertSame('en', $this->getLocale());
    }

    public function testPrefersTheDetectedLocaleToTheAppLocale()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('fr');

        $this->assertSame('en', $this->getLocale('en-CA,en;q=0.9'));
    }

    public function testFallsBackToTheFirstResolvedLocale()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('fr');
        $this->app['locale.manager']->setLocalesResolver(function ($request, $locales) {
            return ['en'];
        });

        $this->assertSame('en', $this->getLocale());
    }
}
