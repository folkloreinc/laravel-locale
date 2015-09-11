<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        
        Config::set('app.locale', 'en');
        
        Config::set('locale', [
            
            'locales' => array(
                'en',
                'fr'
            ),
            
            'detect_from_url' => true,
            
            'store_in_session' => false
        ]);
    }
    
    public function testRouteLocale()
    {
        Route::get('/fr', ['locale' => 'fr', function()
        {
            return config('app.locale');
        }]);
        
        Route::get('/en', [ 'locale' => 'en', function()
        {
            return config('app.locale');
        }]);
        
        Route::get('/es', [ 'locale' => 'es', function()
        {
            return config('app.locale');
        }]);
        
        $this->visit('/fr')
             ->see('fr');
             
        $this->visit('/en')
             ->see('en');
             
        $this->visit('/es')
             ->see(config('app.locale'));
    }
    
    public function testDetectFromUrlSegment()
    {
        Route::get('/fr', function()
        {
            return config('app.locale');
        });
        
        Route::get('/en', function()
        {
            return config('app.locale');
        });
        
        Route::get('/es', function()
        {
            return config('app.locale');
        });
        
        $this->visit('/fr')
             ->see('fr');
             
        $this->visit('/en')
             ->see('en');
             
        $this->visit('/es')
             ->see(config('app.locale'));
    }
    
    public function testStoreInSession()
    {
        Config::set('app.locale', 'en');
        Config::set('locale.store_in_session', true);
        
        Route::get('/fr', ['locale' => 'fr', function()
        {
            return config('app.locale');
        }]);
        
        $this->visit('/fr');
        
        $this->assertEquals(Session::get('locale'), 'fr');
    }
    
    public function testRetrieveFromSession()
    {
        Config::set('app.locale', 'fr');
        Config::set('locale.store_in_session', true);
        
        Route::get('/', [function()
        {
            return config('app.locale');
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
        
    }

    protected function getPackageProviders($app)
    {
        return array('Folklore\LaravelLocale\LocaleServiceProvider');
    }

    protected function getPackageAliases($app)
    {
        return array(
            
        );
    }
}
