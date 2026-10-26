# 0001: Supported versions of 8.x

- Status: accepted
- Date: 2026-10-26

## Context

`8.x` was started for Laravel 8 and kept widening `illuminate/support` up to Laravel 13, with `"php": "^7.3|^8.0.2"`. Its CI, on Travis, had stopped running, and its development dependencies could not install Laravel 11 to 13.

About 20 active projects require `8.x-dev` or `^8.x`. The oldest of them run PHP 8.2 and Laravel 10. The workspace rule is that a maintained line never drops a PHP or Laravel version an active project of the line still uses.

## Decision

- `8.x` keeps its `require`: PHP 7.3+ or 8.0.2+, Laravel 8 to 13. Nothing is dropped within the line.
- CI tests what active projects use: PHP 8.2 to 8.5 with Laravel 10 to 13, within what each Laravel version supports, and `prefer-lowest` once per Laravel version on its lowest PHP version.
- Laravel 8 and 9 and PHP 7.3 to 8.1 stay allowed but untested. They are dropped in the next major, or earlier only if no project could install them anymore.

## Consequences

- The code in `src/` uses no PHP feature newer than PHP 7.3.
- The development dependencies (testbench 8 to 11, PHPUnit 10.5 to 12) only install with PHP 8.1+ and Laravel 10+. Running the tests on an older stack needs older tools.
