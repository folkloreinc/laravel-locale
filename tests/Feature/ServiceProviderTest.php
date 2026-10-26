<?php

namespace Folklore\Locale\Tests\Feature;

use Folklore\Locale\LocaleManager;
use Folklore\Locale\LocaleMiddleware;
use Folklore\Locale\Tests\TestCase;
use Illuminate\Contracts\Http\Kernel;

class ServiceProviderTest extends TestCase
{
    public function test_registers_the_locale_manager()
    {
        $manager = $this->app->make('locale.manager');

        $this->assertInstanceOf(LocaleManager::class, $manager);
        $this->assertSame($manager, $this->app->make(LocaleManager::class));
        $this->assertSame(['en', 'fr'], $manager->locales());
    }

    public function test_pushes_the_locale_middleware()
    {
        $this->assertTrue($this->app->make(Kernel::class)->hasMiddleware(LocaleMiddleware::class));
    }

    public function test_sets_the_locale_of_the_matched_route()
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
