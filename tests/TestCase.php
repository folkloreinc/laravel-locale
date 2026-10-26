<?php

namespace Folklore\Locale\Tests;

use Folklore\Locale\Facade;
use Folklore\Locale\LocaleServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.locale', 'en');

        $app['config']->set('locale', [
            'locales' => ['en', 'fr'],

            'detect_from_url' => true,

            'detect_from_headers' => true,

            'store_in_session' => false,

            'share_with_views' => true,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [LocaleServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'LocaleManager' => Facade::class,
        ];
    }
}
