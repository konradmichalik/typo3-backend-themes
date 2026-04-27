<div align="center">

![Extension icon](Resources/Public/Icons/Extension.png)

# TYPO3 extension `backend_themes`

[![Latest Stable Version](https://typo3-badges.dev/badge/backend_themes/version/shields.svg)](https://extensions.typo3.org/extension/backend_themes)
[![Supported TYPO3 versions](https://typo3-badges.dev/badge/backend_themes/typo3/shields.svg)](https://extensions.typo3.org/extension/backend_themes)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/konradmichalik/typo3-backend-themes/php?logo=php)](https://packagist.org/packages/konradmichalik/typo3-backend-themes)
![Stability](https://typo3-badges.dev/badge/backend_themes/stability/shields.svg)
[![CGL](https://img.shields.io/github/actions/workflow/status/konradmichalik/typo3-backend-themes/cgl.yml?label=cgl&logo=github)](https://github.com/konradmichalik/typo3-backend-themes/actions/workflows/cgl.yml)
[![Tests](https://img.shields.io/github/actions/workflow/status/konradmichalik/typo3-backend-themes/tests.yml?label=tests&logo=github)](https://github.com/konradmichalik/typo3-backend-themes/actions/workflows/tests.yml)
[![License](https://poser.pugx.org/konradmichalik/typo3-backend-themes/license)](LICENSE)

</div>

TYPO3 v14 extension to create custom backend color themes. Define primary and secondary colors, configure dark mode overrides, and let backend users choose their preferred theme.

> [!NOTE]
> Use this extension to subtly establish your project or client branding in the TYPO3 backend. For example, apply corporate colors to the sidebar, header and icons so editors immediately recognize which installation they are working in.

![screencast.gif](Documentation/Images/screencast.gif)!

## ✨ Features

- Custom color themes as database records with live preview
- Dark mode support with optional overrides
- User Settings integration alongside TYPO3 default themes
- Admin-defined default theme recommendation

> [!WARNING]
> This is an experimental extension. TYPO3 v14 introduced the [Fresh theme](https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Feature-108240-IntroduceFreshTheme.html) and the backend theming approach based on CSS custom properties and design tokens is expected to evolve further in upcoming TYPO3 core releases. This extension builds on top of that system and may require adjustments as the core API matures.

## 🔥 Installation

### Requirements

| TYPO3  | PHP       |
|--------|-----------|
| 14.0+  | 8.2 - 8.5 |

### Composer

[![Packagist](https://img.shields.io/packagist/v/konradmichalik/typo3-backend-themes?label=version&logo=packagist)](https://packagist.org/packages/konradmichalik/typo3-backend-themes)
[![Packagist Downloads](https://img.shields.io/packagist/dt/konradmichalik/typo3-backend-themes?color=brightgreen)](https://packagist.org/packages/konradmichalik/typo3-backend-themes)

```bash
composer require konradmichalik/typo3-backend-themes
```

### TER

[![TER version](https://typo3-badges.dev/badge/backend_themes/version/shields.svg)](https://extensions.typo3.org/extension/backend_themes)
[![TER downloads](https://typo3-badges.dev/badge/backend_themes/downloads/shields.svg)](https://extensions.typo3.org/extension/backend_themes)

Download the zip file from [TYPO3 extension repository (TER)](https://extensions.typo3.org/extension/backend_themes).

## 🎨 Configuration

### Creating Themes

1. Open the **List** module at **root level** (pid=0)
2. Create a new **Backend Theme** record
3. Set a **title** and choose a **primary color**
4. Save — the live preview shows light and dark mode side by side

![Theme record with live preview](Documentation/Images/theme-record-preview.jpg)

> [!TIP]
> Check **Default Theme** to mark it as the admin-recommended theme. It will appear at the top of the user dropdown with "(Default)" suffix.

### User Settings

Users select their theme under **User Settings → Appearance → Theme**:

![Theme dropdown in User Settings](Documentation/Images/user-settings-dropdown.jpg)

Standard TYPO3 themes continue to work as before. Custom themes apply color overrides via CSS custom properties.

> [!IMPORTANT]
> After changing a theme in User Settings or editing theme colors, a **full page reload** is required. The extension shows a FlashMessage reminder.

## 🤝 Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development setup, linting, testing and pull request guidelines.

## 📄 License

GPL-2.0-or-later
