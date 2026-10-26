<?php

namespace Folklore\Locale\Tests\Feature;

use Folklore\Locale\LocaleManager;
use Folklore\Locale\LocaleMiddleware;
use Folklore\Locale\Tests\TestCase;
use Illuminate\Contracts\Http\Kernel;

class ServiceProviderTest extends TestCase
{
    public function testRegistersTheLocaleManager()
    {
        $manager = $this->app->make('locale.manager');

        $this->assertInstanceOf(LocaleManager::class, $manager);
        $this->assertSame($manager, $this->app->make(LocaleManager::class));
        $this->assertSame(['en', 'fr'], $manager->locales());
    }

    public function testPushesTheLocaleMiddleware()
    {
        $this->assertTrue($this->app->make(Kernel::class)->hasMiddleware(LocaleMiddleware::class));
    }

    public function testSetsTheLocaleOfTheMatchedRoute()
    {
        $this->app['router']->get('/fr', [
            'locale' => 'fr',
            function () {
                return app()->getLocale();
            },
        ]);

        $this->get('/fr')->assertOk()->assertSeeText('fr');
    }
}
