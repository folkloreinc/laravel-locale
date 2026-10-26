# Changelog

All notable changes to `folklore/laravel-locale` are documented in this file. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- Laravel Pint (`laravel` preset) and Larastan (level 5, with a baseline), checked in CI, and the Composer scripts `format` and `analyse`.
- Tests for the router and URL macros and their helpers.
- Dependabot updates for GitHub Actions and Composer, grouped weekly.

### Changed

- The sources are formatted with Pint instead of the Prettier PHP plugin. Formatting only: the commit is listed in `.git-blame-ignore-revs`.
- `composer.json`: the package type is `library`, with an up-to-date description and a homepage.
- Development files (tests, CI and tool configuration) are left out of the Composer archives.

### Removed

- The Prettier, npm and phpcs configuration.

## [8.2.0] - 2026-10-26

### Added

- `locale.fallback_to_app_locale`: when no locale is detected, the middleware falls back to `app.locale` if it is one of `locale.locales`, and only then to the first entry of `locale.locales` (#1). Off by default, so sites keep the previous behaviour. Sites that ordered `locale.locales` to put their default locale first can enable it instead.
- GitHub Actions: tests on PHP 8.2–8.5 × Laravel 10–13, with `prefer-lowest` once per Laravel version (#2).
- The Composer script `test`.

### Changed

- Development dependencies: `orchestra/testbench` 8 to 11 and `phpunit` 10.5 to 12 (patched releases only), which can install Laravel 11 to 13. The PHP and Laravel versions the package supports don't change.

### Removed

- The Travis and Coveralls configuration.

[Unreleased]: https://github.com/folkloreinc/laravel-locale/compare/v8.2.0...8.x
[8.2.0]: https://github.com/folkloreinc/laravel-locale/compare/v8.1.0...v8.2.0
