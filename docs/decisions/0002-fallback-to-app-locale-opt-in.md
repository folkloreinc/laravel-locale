# 0002: Fallback to `app.locale`, opt-in in 8.x

- Status: accepted
- Date: 2026-10-26

## Context

When no locale is detected, `LocaleMiddleware` used the first entry of `locale.locales` and ignored `app.locale`, the application's default locale ([#1](https://github.com/folkloreinc/laravel-locale/issues/1)). Applications had to keep `locale.locales` ordered with their default locale first, which is easy to break and was not documented.

Falling back to `app.locale` changes the language visitors get on any site whose `app.locale` is not the first entry of `locale.locales`. Many sites install `8.x-dev` directly and would get the change on their next `composer update`.

## Decision

- 8.2.0 adds `locale.fallback_to_app_locale`, `false` by default. When it is enabled, the fallback is `app.locale` if it is one of the supported locales, otherwise the first one.
- A detected locale (request parameter, session, user preference, `Accept-Language`) still wins over the fallback.
- The next major makes it the default.

## Consequences

- Sites that ordered `locale.locales` as a workaround can enable the option and order the list as they like. Niche does.
- `LocaleMiddleware::getFallbackLocale()` holds the fallback, so an application can override it in a subclass.
