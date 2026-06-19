<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_backend_themes" TYPO3 CMS extension.
 *
 * (c) 2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KonradMichalik\Typo3BackendThemes\Service;

use KonradMichalik\Color\Color;
use KonradMichalik\Color\Exception\InvalidColorValue;

/**
 * CssGenerator.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class CssGenerator
{
    /**
     * @param array<string, mixed> $theme
     */
    public function generate(array $theme): string
    {
        $primaryColor = $this->parseColor((string) ($theme['primary_color'] ?? ''));
        if (null === $primaryColor) {
            return '';
        }

        $primary = $primaryColor->toHex();

        $header = $this->parseColor((string) ($theme['header_color'] ?? ''));
        $sidebar = $this->parseColor((string) ($theme['sidebar_color'] ?? ''));
        $dkPrimary = $this->parseColor((string) ($theme['darkmode_primary_color'] ?? ''));
        $dkHeader = $this->parseColor((string) ($theme['darkmode_header_color'] ?? ''));
        $dkSidebar = $this->parseColor((string) ($theme['darkmode_sidebar_color'] ?? ''));

        $sidebarBg = null !== $sidebar ? $sidebar->toHex() : "hsl(from {$primary} h 40% 20%)";
        $headerBg = null !== $header ? $header->toHex() : $sidebarBg;
        $sidebarColor = $this->resolveTextColor($sidebar);
        $headerColor = null !== $header ? $this->resolveTextColor($header) : $sidebarColor;

        $dkEffective = null !== $dkPrimary ? $dkPrimary->toHex() : $primary;
        $dkSidebarBg = null !== $dkSidebar ? $dkSidebar->toHex() : "hsl(from {$dkEffective} h 20% 10%)";
        $dkHeaderBg = null !== $dkHeader ? $dkHeader->toHex() : $dkSidebarBg;

        return <<<CSS
html[data-theme] {
    --token-color-primary-base: {$primary};
    --token-color-secondary-base: color-mix(in srgb, #737373, var(--token-color-primary-base) var(--typo3-color-state-harmonize));
    --typo3-scaffold-header-color: {$headerColor};
    --typo3-scaffold-header-bg: {$headerBg};
    --typo3-scaffold-header-box-shadow: none;
    --typo3-scaffold-sidebar-color: {$sidebarColor};
    --typo3-scaffold-sidebar-bg: {$sidebarBg};
    --typo3-scaffold-sidebar-border-width: 0;
}
html[data-theme] .icon,
html[data-theme] typo3-backend-icon {
    --icon-color-accent: hsl(from {$primary} h s 55%);
}
html[data-theme] .scaffold-sidebar .icon,
html[data-theme] .scaffold-sidebar typo3-backend-icon {
    --icon-color-accent: hsl(from {$primary} h s 75%);
}
html[data-color-scheme="dark"] {
    --token-color-primary-base: {$dkEffective};
    --typo3-scaffold-sidebar-bg: {$dkSidebarBg};
    --typo3-scaffold-header-bg: {$dkHeaderBg};
}
html[data-color-scheme="dark"] .icon,
html[data-color-scheme="dark"] typo3-backend-icon {
    --icon-color-accent: hsl(from {$dkEffective} h s 45%);
}
html[data-color-scheme="dark"] .scaffold-sidebar .icon,
html[data-color-scheme="dark"] .scaffold-sidebar typo3-backend-icon {
    --icon-color-accent: hsl(from {$dkEffective} h s 70%);
}
CSS;
    }

    private function parseColor(string $value): ?Color
    {
        if ('' === $value) {
            return null;
        }

        try {
            return Color::fromHex($value);
        } catch (InvalidColorValue) {
            return null;
        }
    }

    private function resolveTextColor(?Color $bgColor): string
    {
        if (null === $bgColor) {
            return 'var(--typo3-surface-primary-text)';
        }

        return $bgColor->isDark()
            ? 'var(--typo3-surface-primary-text)'
            : 'var(--typo3-text-color-base)';
    }
}
