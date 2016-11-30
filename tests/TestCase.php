<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    
    public function testRouteLocale()
    {
        Route::get('/fr', ['locale' => 'fr', function () {
            return App::getLocale();
        }]);
        
        Route::get('/en', [ 'locale' => 'en', function () {
            return App::getLocale();
        }]);
        
        $this->visit('/fr')
             ->see('fr');
             
        $this->visit('/en')
             ->see(App::getLocale());
    }
    
    public function testDetectFromUrlSegment()
    {
        Route::get('/fr', function () {
            return App::getLocale();
        });
        
        Route::get('/en', function () {
            return App::getLocale();
        });
        
        $this->visit('/fr')
             ->see('fr');
             
        $this->visit('/en')
             ->see(App::getLocale());
    }
    
    public function testStoreInSession()
    {
        Config::set('locale.store_in_session', true);
        App::setLocale('en');
        
        Route::get('/fr', ['locale' => 'fr', function () {
            return App::getLocale();
        }]);
        
        $this->visit('/fr');
        
        $this->assertEquals(Session::get('locale'), 'fr');
    }
    
    public function testRetrieveFromSession()
    {
        Config::set('locale.store_in_session', true);
        App::setLocale('fr');
        
        Route::get('/', [function () {
            return App::getLocale();
        }]);
        
        $this->withSession(['locale' => 'en'])
            ->visit('/')
            ->see('en');
    }
    
    public function testViewShare()
    {
        Config::set('app.locale', 'en');
        App::setLocale('fr');
        
        $this->assertEquals(View::shared('locale'), 'fr');
        $this->assertEquals(View::shared('otherLocales'), ['en']);
    }
    
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.locale', 'en');
        
        $app['config']->set('locale', [
            
            'locales' => array(
                'en',
                'fr'
            ),
            
            'detect_from_url' => true,
            
            'detect_from_headers' => true,
            
            'store_in_session' => false,
            
            'share_with_views' => true
        ]);
    }

    protected function getPackageProviders($app)
    {
        return array('Folklore\LaravelLocale\LocaleServiceProvider');
    }

    protected function getPackageAliases($app)
    {
        return array(
            'LocaleManager'  => 'Folklore\LaravelLocale\LocaleFacade'
        );
    }
}
