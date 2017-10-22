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

        Route::get('/en', [ 'locale' => 'en', function () {
            return app()->getLocale();
        }]);

        $response = $this->get('/fr');
        $this->assertEquals('fr', $response->getContent());

        $response = $this->get('/en');
        $this->assertEquals('en', $response->getContent());
    }

    public function testDetectFromUrlSegment()
    {
        Route::get('/fr', function () {
            return app()->getLocale();
        });

        Route::get('/en', function () {
            return app()->getLocale();
        });

        $response = $this->get('/fr');
        $this->assertEquals('fr', $response->getContent());

        $response = $this->get('/en');
        $this->assertEquals('en', $response->getContent());
    }

    public function testStoreInSession()
    {
        Config::set('locale.store_in_session', true);
        app()->setLocale('en');

        Route::get('/fr', ['locale' => 'fr', function () {
            return app()->getLocale();
        }]);

        $this->get('/fr');
        $this->assertEquals('fr', Session::get('locale'));
    }

    public function testRetrieveFromSession()
    {
        Config::set('locale.store_in_session', true);
        app()->setLocale('fr');

        Route::get('/', [function () {
            return app()->getLocale();
        }]);

        $response = $this->withSession(['locale' => 'en'])
            ->get('/');
        $this->assertEquals('en', $response->getContent());
    }

    public function testViewShare()
    {
        Config::set('app.locale', 'en');
        app()->setLocale('fr');

        $this->assertEquals('fr', View::shared('locale'));
        $this->assertEquals(['en'], View::shared('otherLocales'));
    }
}
