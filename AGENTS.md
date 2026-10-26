# AGENTS.md

`folklore/laravel-locale` is a **public** Laravel package for locale detection, localized routes and localized URLs. It is published on Packagist. `folklore/laravel-folklore` depends on it, and so do many client sites, Micromag and Niche.

## Language

**Everything in this repository is written in English**, because the package is public:

- code, identifiers, comments and file names;
- documentation, README and changelog;
- commit messages;
- GitHub issues and their comments, pull request titles and bodies, and review comments.

This applies even when the conversation with the maintainer happens in another language (often French).

## Roadmap

[docs/roadmap.md](docs/roadmap.md) is the plan: `8.x` takes fixes, security and additive options; ideas for a 9.0 are listed there, none of them planned. Decisions are in [docs/decisions](docs/decisions).

## Branches and releases

- `8.x` is the maintained branch and the default branch. `master` and `develop` are stale; don't base work on them.
- `8.x` allows **PHP 7.3+ or 8.0.2+ and Laravel 8 to 13**. CI tests PHP 8.2 to 8.5 with Laravel 10 to 13, the versions active projects use ([decisions/0001](docs/decisions/0001-supported-versions.md)). Don't drop a version within `8.x`, and don't use PHP features newer than PHP 7.3 in `src/`.
- Follow semver and tag releases (`v8.2.0`, …). Consumers should require `^8.2` instead of `8.x-dev`.

## Pull requests

Many sites install `8.x-dev` directly, so every merge to `8.x` reaches production on their next `composer update`. **Every change goes through a pull request; never push to `8.x` directly.**

1. Start from an issue. Read it and its comments first.
2. Branch from an up-to-date `8.x`, named `<type>/<issue>-<slug>`: `fix/12-session-locale`, `feature/…`, `docs/…`, `ci/…`, `refactor/…`.
3. Commit as you go, then push the branch and open a pull request against `8.x`, with `Closes #<issue>` (or `Part of #<issue>`) in its body. Write the title and body in English, and list the checks you ran.
4. Merge only when CI is green and the pull request is reviewed. A pull request with a formatting-only commit is merged with a merge commit, so the commit keeps its hash in `.git-blame-ignore-revs`.
5. The branch is deleted when its pull request merges. An issue closes when its pull request merges, not when code is pushed.

Something out of scope noticed along the way becomes a new issue, not part of the current pull request.

## Compatibility rules

Within `8.x`:

- Changes are additive and opt-in: defaults keep the current behaviour. A change of default waits for the next major, behind an option until then (as `fallback_to_app_locale`, [decisions/0002](docs/decisions/0002-fallback-to-app-locale-opt-in.md)).
- Keep public signatures and behaviour stable:
    - the middleware's detection order;
    - the `Router` macros (`groupWithLocales`, `locale`, `getTrans` and the other `*Trans` methods, `resourceTrans`, `nameWithLocale`), the `Route` macros (`locale`, `nameWithLocale`) and `UrlGenerator::routeWithLocale()`;
    - the `route_with_locale()` and `route_name_with_locale()` helpers, and `Request::locale()`;
    - `LocaleManager` and the `locale.manager` binding;
    - the existing config keys and their defaults.

## Repository map

```
src/Folklore/Locale/   Service provider, middleware, manager, macros (mixins), helpers
src/config/locale.php  Default config, published to config/locale.php
tests/Feature/         PHPUnit with Orchestra Testbench
docs/                  Roadmap and decisions
```

## Tests and CI

Every change ships with automated tests:

- A bug fix adds a **regression test that fails without the fix** and passes with it.
- A new option is tested in both states, through a real request when it affects the middleware or the routes.

CI (GitHub Actions, `.github/workflows`) runs:

- `run-tests.yml`: the suite with `prefer-stable` on every PHP × Laravel combination tested, and with `prefer-lowest` on the lowest PHP version of each Laravel version, and a `Tests passed` job that succeeds only when all of them do;
- `pint.yml` and `phpstan.yml`: code style, static analysis, `composer validate --strict` and the autoload.

A change merges only when CI is green.

## Tooling

| Concern  | Tool                                             | Command            |
| -------- | ------------------------------------------------ | ------------------ |
| Tests    | PHPUnit with Orchestra Testbench                 | `composer test`    |
| Style    | Laravel Pint (`laravel` preset, `pint.json`)     | `composer format`  |
| Analysis | Larastan (`phpstan.neon.dist`)                   | `composer analyse` |
| CI       | GitHub Actions (`.github/workflows`)             |                    |

Don't add other formatters or linters for PHP, such as `phpcs` or the Prettier PHP plugin.

## Checks

Before committing, run `composer test`, `composer analyse` and `composer format`.

- **Larastan baseline:** `phpstan-baseline.neon` lists known errors. Fixing one removes its entry (regenerate with `vendor/bin/phpstan analyse --generate-baseline`). Never add new code to the baseline: fix the error instead.
- **Formatting-only commits** (for example a new Pint rule) go in their own commit, listed in `.git-blame-ignore-revs`.
- **Changelog:** add user-facing changes to the `[Unreleased]` section of `CHANGELOG.md`.
