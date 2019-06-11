<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Folklore\LaravelLocale\LocaleChanged;

class LocaleManagerTest extends TestCase
{
    public function testGetLocale()
    {
        $this->assertEquals(app()->getLocale(), app('locale.manager')->getLocale());
    }

    public function testAppGetLocale()
    {
        app()->setLocale('fr');
        $this->assertEquals('fr', app('locale.manager')->getLocale());
    }

    public function testSetLocale()
    {
        app('locale.manager')->setLocale('fr');
        $this->assertEquals(app()->getLocale(), 'fr');
    }

    public function testGetOtherLocales()
    {
        app()->setLocale('fr');
        $this->assertEquals(app('locale.manager')->getOtherLocales(), ['en']);
    }

    public function testFireEvent()
    {
        $obj = new StdClass();
        $obj->called = false;
        app('events')->listen(LocaleChanged::class, function () use ($obj) {
            $obj->called = true;
        });
        app('locale.manager')->setLocale('fr');
        $this->assertTrue($obj->called);
    }

    public function testStoreInSession()
    {
        Config::set('locale.store_in_session', true);
        app()->setLocale('fr');
        $this->assertEquals('fr', Session::get('locale'));
    }

    public function testViewShare()
    {
        Config::set('locale.share_with_views', true);
        app()->setLocale('fr');
        $this->assertEquals('fr', View::shared('locale'));
        $this->assertEquals(['en'], View::shared('otherLocales'));
    }
}
