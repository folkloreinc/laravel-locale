# Contributing

Thanks for helping! Contributions are welcome through issues and pull requests on [GitHub](https://github.com/folkloreinc/laravel-locale).

- Everything in this repository is written in **English**: code, comments, commits, issues and pull requests.
- Base your work on the `8.x` branch and open a pull request against it; nothing is pushed to `8.x` directly. Name your branch `<type>/<issue>-<slug>` (for example `fix/12-session-locale`). The plan is in [docs/roadmap.md](docs/roadmap.md).
- Don't change defaults or public APIs within `8.x`: new behaviour is opt-in. See the compatibility rules in [AGENTS.md](AGENTS.md).

## Tests and checks

Every change ships with tests. A bug fix adds a test that fails without the fix.

```bash
composer install
composer test        # PHPUnit
composer analyse     # Larastan
composer format      # Laravel Pint
```

A pull request is merged only when CI is green.

## Releasing

Maintainers release from `8.x`, following [Semantic Versioning](https://semver.org/):

1. Open a release pull request that moves the `[Unreleased]` section of `CHANGELOG.md` to the new version with its date, with upgrade notes when sites will see a change.
2. Once it's merged and CI is green on `8.x`, tag the merge commit (`git tag v8.3.0 && git push origin v8.3.0`) and publish a GitHub release with the changelog section. Packagist picks up the tag.

## Security

Please don't report vulnerabilities in public issues. See [SECURITY.md](SECURITY.md).
