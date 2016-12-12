<?php namespace Folklore\LaravelLocale;

class LocaleChanged
{
    public $locale;
    
    public function __construct($locale)
    {
        $this->locale = $locale;
    }
}
