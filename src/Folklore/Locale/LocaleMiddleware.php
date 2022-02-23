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
        $browserLocale = $this->getFromHeaders($request);
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
        $parameter = config('locale.request_parameter');
        return isset($parameter) ? $request->input($parameter) : null;
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

    protected function getFromHeaders($request): ?string
    {
        if (!config('locale.detect_from_headers', true)) {
            return null;
        }
        $acceptLanguage = $request->headers->get('Accept-Language');
        if (empty($acceptLanguage)) {
            return null;
        }
        $locales = config('locale.locales', config('app.locales', [app()->getLocale()]));
        return collect(explode(',', $acceptLanguage))
            ->map(function ($lang) {
                return trim(explode('-', strtolower(trim(explode(';', trim($lang))[0])))[0]);
            })
            ->fitler(function ($lang) use ($locales) {
                return !empty($lang) && in_array($lang, $locales);
            })
            ->first();
    }
}
