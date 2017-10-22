<?php namespace Folklore\LaravelLocale;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Session\Session;

class LocaleMiddleware
{
    protected $session;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $storeInSession = config('locale.store_in_session');
        $locale = $storeInSession && $this->session->has('locale') ? $this->session->get('locale'):'auto';

        $defaultLocale = app()->getLocale();
        $locales = config('locale.locales');

        // Try to detect it from the request url
        $detectFromUrl = config('locale.detect_from_url');
        if ($detectFromUrl) {
            $segment = $request->segment(1);
            if ($segment && in_array($segment, $locales)) {
                $locale = $segment;
            }
        }

        // Try to detect it from the request headers
        $detectFromHeaders = config('locale.detect_from_headers', true);
        if ($locale !== 'auto' && $locale !== $defaultLocale) {
            app()->setLocale($locale);
        } elseif ($locale === 'auto' && $detectFromHeaders) {
            $browserLang = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ?
                strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ','):'';
            $browserLang = substr($browserLang, 0, 2);
            $userLang = in_array($browserLang, $locales) ? $browserLang : $defaultLocale;
            app()->setLocale($userLang);
        }

        return $next($request);
    }
}
