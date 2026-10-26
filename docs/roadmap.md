# Roadmap

`8.x` is the maintained line: fixes, security and additive, opt-in options ([AGENTS.md](../AGENTS.md#compatibility-rules)). Each release says what it shipped in the [CHANGELOG](../CHANGELOG.md). There is no parent issue: each piece of work starts from its own issue.

## 8.x

- [x] CI on GitHub Actions, PHP 8.2 to 8.5 × Laravel 10 to 13 ([decisions/0001](decisions/0001-supported-versions.md), #2, 8.2.0)
- [x] Opt-in fallback to `app.locale` ([decisions/0002](decisions/0002-fallback-to-app-locale-opt-in.md), #1, 8.2.0)
- [x] Laravel Pint instead of the Prettier PHP plugin, Larastan, Dependabot (#4)
- [x] README, `AGENTS.md`, contributing guide, security policy, these docs
- [ ] `route_with_locale()` and `UrlGenerator::routeWithLocale()` ignore `$absolute` when the second argument is an array of parameters
- [ ] `Route::locale($locale, true)` appends the localized name to an existing name (`contactfr.contact`) instead of replacing it
- [ ] Clear the Larastan baseline, mostly by typing the macro closures
- [ ] Consumers move from `8.x-dev` to `^8.2` (tracked in each project)

## Ideas for 9.0 (not planned)

A 9.0 starts only when a project needs a breaking change. It would:

- make `fallback_to_app_locale` the default ([decisions/0002](decisions/0002-fallback-to-app-locale-opt-in.md));
- require PHP 8.2+ and Laravel 10+, the stack floor of the active projects at that time;
- autoload with PSR-4 from `src/`, and type the public API.
