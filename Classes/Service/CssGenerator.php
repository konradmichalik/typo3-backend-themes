<?php

declare(strict_types=1);

/*
 * This file is part of the "backend_themes" TYPO3 CMS extension.
 *
 * (c) 2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KonradMichalik\Typo3BackendThemes\Service;

/**
 * CssGenerator.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class CssGenerator
{
    private const HEX_COLOR_PATTERN = '/^#[A-Fa-f0-9]{6}$/';

    /**
     * @param array<string, mixed> $theme
     */
    public function generate(array $theme): string
    {
        $primary = (string) ($theme['primary_color'] ?? '');
        if (!$this->isValidHexColor($primary)) {
            return '';
        }

        $header = $this->validateColor((string) ($theme['header_color'] ?? ''));
        $sidebar = $this->validateColor((string) ($theme['sidebar_color'] ?? ''));
        $dkPrimary = $this->validateColor((string) ($theme['darkmode_primary_color'] ?? ''));
        $dkHeader = $this->validateColor((string) ($theme['darkmode_header_color'] ?? ''));
        $dkSidebar = $this->validateColor((string) ($theme['darkmode_sidebar_color'] ?? ''));

        $sidebarBg = '' !== $sidebar ? $sidebar : "hsl(from {$primary} h 40% 20%)";
        $headerBg = '' !== $header ? $header : $sidebarBg;
        $sidebarColor = $this->resolveTextColor($sidebar);
        $headerColor = '' !== $header ? $this->resolveTextColor($header) : $sidebarColor;

        $dkEffective = '' !== $dkPrimary ? $dkPrimary : $primary;
        $dkSidebarBg = $dkSidebar ?: "hsl(from {$dkEffective} h 20% 10%)";
        $dkHeaderBg = $dkHeader ?: $dkSidebarBg;

        $css = <<<CSS
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

        return $css;
    }

    private function isValidHexColor(string $color): bool
    {
        return 1 === preg_match(self::HEX_COLOR_PATTERN, $color);
    }

    private function validateColor(string $color): string
    {
        return '' !== $color && $this->isValidHexColor($color) ? $color : '';
    }

    private function resolveTextColor(string $bgColor): string
    {
        if ('' === $bgColor) {
            return 'var(--typo3-surface-primary-text)';
        }

        return $this->isDarkColor($bgColor)
            ? 'var(--typo3-surface-primary-text)'
            : 'var(--typo3-text-color-base)';
    }

    private function isDarkColor(string $hex): bool
    {
        $r = hexdec(substr($hex, 1, 2)) / 255;
        $g = hexdec(substr($hex, 3, 2)) / 255;
        $b = hexdec(substr($hex, 5, 2)) / 255;

        return (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) < 0.5;
    }

}
