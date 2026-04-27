# TYPO3 Backend Themes

[![Tests](https://github.com/konradmichalik/typo3-backend-themes/actions/workflows/tests.yml/badge.svg)](https://github.com/konradmichalik/typo3-backend-themes/actions/workflows/tests.yml)
[![CGL](https://github.com/konradmichalik/typo3-backend-themes/actions/workflows/cgl.yml/badge.svg)](https://github.com/konradmichalik/typo3-backend-themes/actions/workflows/cgl.yml)

TYPO3 extension to configure and select custom backend color themes with primary/secondary colors, dark mode support and live preview.

## Features

- Create theme records with primary and secondary colors
- Automatic secondary color derivation from primary
- Optional dark mode color overrides
- Live preview when editing theme records
- Users select themes in User Settings
- Global default theme support

## Requirements

- TYPO3 v14+
- PHP 8.2+

## Installation

```bash
composer require konradmichalik/typo3-backend-themes
```

## Documentation

See the [Documentation](Documentation/Index.rst) for detailed configuration and usage instructions.

## Development

```bash
ddev start
ddev install 14
ddev launch 14 /typo3
```

### CGL

```bash
ddev cgl lint
ddev cgl fix
ddev cgl sca
```

### Tests

```bash
ddev exec composer test
ddev exec composer test:coverage
```

## License

GPL-2.0-or-later
