<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Orchestra\Testbench\TestCase as BaseTestCase;

class LocaleManagerTestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    
    public function testGetLocale()
    {
        $this->assertEquals(App::getLocale(), LocaleManager::getLocale());
    }
    
    public function testSetLocale()
    {
        LocaleManager::setLocale('fr');
        
        $this->assertEquals(App::getLocale(), 'fr');
    }
    
    public function testGetOtherLocales()
    {
        App::setLocale('fr');
        
        $this->assertEquals(LocaleManager::getOtherLocales(), ['en']);
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
