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

        $secondary = $this->validateColor((string) ($theme['secondary_color'] ?? ''));
        $dkPrimary = $this->validateColor((string) ($theme['darkmode_primary_color'] ?? ''));
        $dkSecondary = $this->validateColor((string) ($theme['darkmode_secondary_color'] ?? ''));

        $scaffoldBg = '' !== $secondary
            ? $secondary
            : 'light-dark(hsl(from var(--token-color-primary-base) h 40% 20%), hsl(from var(--token-color-primary-base) h 20% 10%))';

        $css = <<<CSS
html[data-theme] {
    --token-color-primary-base: {$primary};
    --token-color-secondary-base: color-mix(in srgb, #737373, var(--token-color-primary-base) var(--typo3-color-state-harmonize));
    --typo3-icons-accent: light-dark(hsl(from {$primary} h s 55%), hsl(from {$primary} h s 45%));
    --icon-color-accent: var(--typo3-icons-accent);
    --typo3-scaffold-header-color: var(--typo3-surface-primary-text);
    --typo3-scaffold-header-bg: {$scaffoldBg};
    --typo3-scaffold-header-box-shadow: none;
    --typo3-scaffold-sidebar-color: var(--typo3-surface-primary-text);
    --typo3-scaffold-sidebar-bg: {$scaffoldBg};
    --typo3-scaffold-sidebar-border-width: 0;
}
html[data-theme] .icon,
html[data-theme] typo3-backend-icon {
    --icon-color-accent: light-dark(hsl(from {$primary} h s 55%), hsl(from {$primary} h s 45%));
}
html[data-theme] .scaffold-sidebar .icon,
html[data-theme] .scaffold-sidebar typo3-backend-icon {
    --icon-color-accent: light-dark(hsl(from {$primary} h s 75%), hsl(from {$primary} h s 70%));
}
CSS;

        if ('' !== $dkPrimary || '' !== $dkSecondary) {
            $darkLines = [];
            if ('' !== $dkPrimary) {
                $darkLines[] = "    --token-color-primary-base: {$dkPrimary};";
            }
            if ('' !== $dkSecondary) {
                $darkLines[] = "    --typo3-scaffold-header-bg: {$dkSecondary};";
                $darkLines[] = "    --typo3-scaffold-sidebar-bg: {$dkSecondary};";
            } elseif ('' !== $dkPrimary && '' === $secondary) {
                $darkLines[] = '    --typo3-scaffold-header-bg: hsl(from var(--token-color-primary-base) h 20% 10%);';
                $darkLines[] = '    --typo3-scaffold-sidebar-bg: hsl(from var(--token-color-primary-base) h 20% 10%);';
            }
            if ([] !== $darkLines) {
                $inner = implode("\n", $darkLines);
                $css .= <<<CSS

html[data-color-scheme="dark"] {
{$inner}
}
CSS;
            }
        }

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
}
