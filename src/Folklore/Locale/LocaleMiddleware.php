<?php namespace Folklore\Locale;

use Closure;
use Illuminate\Contracts\Translation\HasLocalePreference;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\ $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currentLocale = app()->getLocale();
        $locales = config('locale.locales', config('app.locales', [$currentLocale]));

        // Get locale from request or session
        $requestLocale = $this->getFromRequest($request);
        $sessionLocale = $this->getFromSession($request);
        $userLocale = $this->getFromUser($request);
        $browserLocale = $this->getFromRequestHeaders($request, $locales);
        $detectedLocale =
            $requestLocale ?? ($sessionLocale ?? ($userLocale ?? ($browserLocale ?? null)));
        $newLocale = in_array($detectedLocale, $locales) ? $detectedLocale : $locales[0];

        // Set new locale
        if (!is_null($newLocale) && $newLocale !== $currentLocale) {
            app()->setLocale($newLocale);
        }

        // prettier-ignore
        if ($request->hasSession() &&
            config('locale.store_in_session', true) &&
            $newLocale !== $sessionLocale
        ) {
            $request->session()->put('locale', $newLocale);
        }

        return $next($request);
    }

    protected function getFromRequest($request): ?string
    {
        $route = $request->route();
        $routeLocale = data_get(
            !is_null($route) ? $route->getAction() : null,
            'locale',
            $request->route('locale')
        );
        return $request->input('locale', $routeLocale);
    }

    protected function getFromSession($request): ?string
    {
        if (!config('locale.store_in_session', true)) {
            return null;
        }
        return $request->hasSession() ? $request->session()->get('locale') : null;
    }

    protected function getFromUser($request): ?string
    {
        if (!config('locale.uses_user_locale_preference', true)) {
            return null;
        }
        $user = $request->user();
        return !is_null($user) && $user instanceof HasLocalePreference
            ? $user->preferredLocale()
            : null;
    }

    protected function getFromRequestHeaders($request): ?string
    {
        if (!config('locale.detect_from_headers', true)) {
            return null;
        }
        $acceptLanguage = $request->headers->get('Accept-Language');
        $browserLang = !empty($acceptLanguage) ? strtok(strip_tags($acceptLanguage), ',') : '';
        $browserLang = strtolower(substr($browserLang, 0, 2));
        return $browserLang;
    }
}
