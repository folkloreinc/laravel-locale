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

    public function test_falls_back_to_the_first_locale_by_default()
    {
        $this->app->setLocale('fr');

        $this->assertSame('en', $this->getLocale());
    }

    public function test_falls_back_to_the_app_locale_when_enabled()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('fr');

        $this->assertSame('fr', $this->getLocale());
    }

    public function test_falls_back_to_the_first_locale_when_the_app_locale_is_not_supported()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('de');

        $this->assertSame('en', $this->getLocale());
    }

    public function test_prefers_the_detected_locale_to_the_app_locale()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('fr');

        $this->assertSame('en', $this->getLocale('en-CA,en;q=0.9'));
    }

    public function test_falls_back_to_the_first_resolved_locale()
    {
        config(['locale.fallback_to_app_locale' => true]);
        $this->app->setLocale('fr');
        $this->app['locale.manager']->setLocalesResolver(function ($request, $locales) {
            return ['en'];
        });

        $this->assertSame('en', $this->getLocale());
    }
}
