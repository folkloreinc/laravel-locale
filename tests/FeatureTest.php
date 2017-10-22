<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeatureTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRouteLocale()
    {
        Route::get('/fr', ['locale' => 'fr', function () {
            return app()->getLocale();
        }]);

        Route::get('/en', ['locale' => 'en', function () {
            return app()->getLocale();
        }]);

        $response = $this->call('GET', '/fr');
        $this->assertEquals('fr', $response->getContent());

        $response = $this->call('GET', '/en');
        $this->assertEquals('en', $response->getContent());
    }

    public function testDetectFromUrlSegment()
    {
        Config::set('locale.detect_from_url', true);
        Route::get('/fr', function () {
            return app()->getLocale();
        });

        Route::get('/en', function () {
            return app()->getLocale();
        });

        $response = $this->call('GET', '/fr');
        $this->assertEquals('fr', $response->getContent());

        $response = $this->call('GET', '/en');
        $this->assertEquals('en', $response->getContent());
    }

    public function testRetrieveFromSession()
    {
        Config::set('locale.store_in_session', true);
        app()->setLocale('fr');

        Route::get('/', [function () {
            return app()->getLocale();
        }]);

        Session::put('locale', 'en');
        $response = $this->call('GET', '/');
        $this->assertEquals('en', $response->getContent());
    }
}
