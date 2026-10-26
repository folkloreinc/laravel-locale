<?php

namespace Folklore\Locale\Tests\Feature;

use Folklore\Locale\Tests\TestCase;

class RoutingTest extends TestCase
{
    protected function refreshRoutes()
    {
        // Names set after a route is added are only looked up once the collection is refreshed,
        // as the framework does after loading the route files.
        $this->app['router']->getRoutes()->refreshNameLookups();
    }

    public function testGroupsRoutesByLocale()
    {
        $router = $this->app['router'];
        $router->groupWithLocales(function ($locale) use ($router) {
            $router
                ->get('/', function () {
                    return app()->getLocale();
                })
                ->nameWithLocale('home');
        });
        $this->refreshRoutes();

        $this->assertSame('http://localhost/en', route_with_locale('home', 'en'));
        $this->assertSame('http://localhost/fr', route_with_locale('home', 'fr'));
        $this->get('/fr')->assertOk()->assertSeeText('fr');
        $this->get('/en')->assertOk()->assertSeeText('en');
    }

    public function testGroupsRoutesByLocaleWithAPrefix()
    {
        $router = $this->app['router'];
        $router->groupWithLocales(['prefix' => 'blog'], function ($locale) use ($router) {
            $router
                ->get('/', function () {
                    return app()->getLocale();
                })
                ->nameWithLocale('blog');
        });
        $this->refreshRoutes();

        $this->assertSame('http://localhost/fr/blog', route_with_locale('blog', 'fr'));
        $this->get('/fr/blog')->assertOk()->assertSeeText('fr');
    }

    public function testTranslatesRouteUris()
    {
        $this->app['translator']->addLines(['routes.about' => 'a-propos'], 'fr');

        $router = $this->app['router'];
        $router->groupWithLocales(function ($locale) use ($router) {
            $router
                ->getTrans('about', function () {
                    return app()->getLocale();
                })
                ->nameWithLocale('about');
        });
        $this->refreshRoutes();

        $this->assertSame('http://localhost/fr/a-propos', route_with_locale('about', 'fr'));
        $this->assertSame('http://localhost/en/about', route_with_locale('about', 'en'));
        $this->get('/fr/a-propos')->assertOk()->assertSeeText('fr');
    }

    public function testGeneratesTheUrlOfTheCurrentLocale()
    {
        $router = $this->app['router'];
        $router->groupWithLocales(function ($locale) use ($router) {
            $router->get('/', function () {})->nameWithLocale('home');
        });
        $this->refreshRoutes();

        $this->app->setLocale('fr');

        $this->assertSame('http://localhost/fr', route_with_locale('home'));
        $this->assertSame('/fr', route_with_locale('home', 'fr', [], false));
    }

    public function testSetsTheLocaleOfARoute()
    {
        $this->app['router']
            ->get('/contact', function () {
                return app()->getLocale();
            })
            ->locale('fr');

        $this->get('/contact')->assertOk()->assertSeeText('fr');
    }

    public function testNamesRoutesWithALocale()
    {
        $this->assertSame('fr.home', route_name_with_locale('home', 'fr'));

        $this->app->setLocale('fr');

        $this->assertSame('fr.home', route_name_with_locale('home'));
    }
}
