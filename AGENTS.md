# AGENTS.md

Guidance for coding agents working in this repository.

## Project overview

`konradmichalik/typo3-backend-themes` is a TYPO3 extension (`typo3_backend_themes`) that lets editors choose custom backend color themes: a primary color, optional sidebar and header overrides, and dark mode overrides. Themes are database records (`tx_typo3backendthemes_theme`, root level) and are applied by injecting CSS custom properties into every backend response. The underlying TYPO3 backend theming system (design tokens) is still evolving.

- PHP: `~8.2.0 || ~8.3.0 || ~8.4.0 || ~8.5.0`
- TYPO3: `^14.3.5` (`typo3/cms-core`, `typo3/cms-backend`)
- Color handling: `konradmichalik/php-color`
- License: GPL-2.0-or-later

## Structure

```
Classes/
  Backend/Form/    ThemePreviewElement (FormEngine node), ThemeItemsProcFunc (theme dropdown)
  Controller/      CssController (AJAX route returning generated CSS)
  Hook/            DataHandlerHook (single default theme flag)
  Middleware/      ThemeCssInjectionMiddleware
  Service/         ThemeService, CssGenerator
Configuration/     TCA, Services.yaml, RequestMiddlewares.php, Backend/AjaxRoutes.php, JavaScriptModules.php, Icons.php
Resources/
  Public/JavaScript/  theme-live.js, theme-preview.js
  Private/Language, Private/Libs
Tests/
  Unit/            Unit tests
  CGL/             Separate composer project for code style, static analysis and rector
Documentation/     Extension documentation
.ddev/             DDEV setup, TYPO3 instance under .Build/14
```

How the pieces fit:
- `ThemeService::resolveUserTheme()` is the only mapping from `$BE_USER->uc['theme']` to a theme record. Custom themes are stored as `custom_<uid>`, anything else is a stock TYPO3 theme and the extension does nothing.
- `ThemeCssInjectionMiddleware` (after authentication, before output compression) asks `CssGenerator` for a CSS block and adds it inline through `PageRenderer::addCssInlineBlock`.
- `CssGenerator` validates colors via `Color::fromHex()`, overrides TYPO3 design tokens and derives dark mode and accent colors with `light-dark()` and relative `hsl(from ...)`. Change these derivation rules with care.
- `theme-live.js` fetches fresh CSS from the `backend_themes_css` AJAX route to re-style the shell after a theme change without a full reload.

Where to add things:
- New theme field: TCA in `Configuration/TCA`, `ext_tables.sql`, `CssGenerator`, and a test in `Tests/Unit/Service/CssGeneratorTest.php`.
- New CSS variable: `CssGenerator::generate()` only. The extension injects CSS inline on purpose, do not add CSS files.
- New backend behavior tied to themes: a middleware in `Classes/Middleware` plus an entry in `Configuration/RequestMiddlewares.php`.

## Development commands

All development happens inside DDEV.

```bash
ddev start
ddev composer install
ddev install 14            # TYPO3 v14 instance in .Build/14
ddev launch
ddev 14 typo3 cache:flush
composer docs              # build the documentation with docker compose
```

## Testing

```bash
ddev composer test
ddev composer test:coverage
ddev composer test -- --filter CssGeneratorTest
```

- Only a unit suite exists (`phpunit.xml`, `Tests/Unit`). There is no functional or E2E suite.
- Mock `ConnectionPool` and `QueryBuilder` instead of booting TYPO3.
- CI runs the tests through a reusable workflow on PHP 8.2 to 8.5 against TYPO3 14.2.

## Code style and static analysis

```bash
ddev cgl lint              # composer normalize, editorconfig, PHP-CS-Fixer (dry run)
ddev cgl fix
ddev cgl sca               # PHPStan
ddev cgl migration         # Rector
```

- PHP-CS-Fixer with `konradmichalik/php-cs-fixer-preset` (`Tests/CGL/.php-cs-fixer.php`). Run `ddev cgl fix:php` instead of formatting by hand. The file header copyright year is set in `composer.json` under `extra.konradmichalik/php-cs-fixer-preset`.
- PHPStan level 8 with `konradmichalik/phpstan-typo3-preset` and a baseline (`Tests/CGL/phpstan.neon`).
- Services are `final readonly` with constructor injection. `Configuration/Services.yaml` autowires `Classes/`, only services used by FormEngine or DataHandler hooks are `public`.
- Strict types and PHP 8.2 syntax throughout.
- CI runs the CGL workflow through a reusable workflow.

## Git workflow

- Commit format: `<type>: <description>`
- Types: `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`, `ci`
- No co-author trailers
- One commit per logical change
- Open a pull request with a clear description.
