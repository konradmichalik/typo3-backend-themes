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
     * Generate CSS custom property overrides from a theme configuration array.
     *
     * @param array<string, mixed> $theme
     */
    public function generate(array $theme): string
    {
        $primaryColor = (string) ($theme['primary_color'] ?? '');

        if (!$this->isValidHexColor($primaryColor)) {
            return '';
        }

        $secondaryColor = $this->validateColor((string) ($theme['secondary_color'] ?? ''));
        $darkmodePrimary = $this->validateColor((string) ($theme['darkmode_primary_color'] ?? ''));
        $darkmodeSecondary = $this->validateColor((string) ($theme['darkmode_secondary_color'] ?? ''));

        $scaffoldBg = '' !== $secondaryColor
            ? $secondaryColor
            : 'light-dark(hsl(from var(--token-color-primary-base) h 40% 20%), hsl(from var(--token-color-primary-base) h 20% 10%))';

        $css = $this->buildRootBlock($primaryColor, $scaffoldBg);
        $css .= "\n";
        $css .= $this->buildIconAccentBlock($primaryColor);

        if ('' !== $darkmodePrimary || '' !== $darkmodeSecondary) {
            $css .= "\n";
            $css .= $this->buildDarkModeBlock($darkmodePrimary, $darkmodeSecondary, '' === $secondaryColor);
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

    private function buildRootBlock(string $primaryColor, string $scaffoldBg): string
    {
        return <<<CSS
html[data-theme] {
    --token-color-primary-base: {$primaryColor};
    --token-color-secondary-base: color-mix(in srgb, #737373, var(--token-color-primary-base) var(--typo3-color-state-harmonize));
    --typo3-icons-accent: light-dark(hsl(from {$primaryColor} h s 55%), hsl(from {$primaryColor} h s 45%));
    --icon-color-accent: var(--typo3-icons-accent);
    --typo3-scaffold-header-color: var(--typo3-surface-primary-text);
    --typo3-scaffold-header-bg: {$scaffoldBg};
    --typo3-scaffold-header-box-shadow: none;
    --typo3-scaffold-sidebar-color: var(--typo3-surface-primary-text);
    --typo3-scaffold-sidebar-bg: {$scaffoldBg};
    --typo3-scaffold-sidebar-border-width: 0;
}
CSS;
    }

    private function buildIconAccentBlock(string $primaryColor): string
    {
        return <<<CSS
html[data-theme] .scaffold-sidebar {
    --typo3-icons-accent: light-dark(hsl(from {$primaryColor} h s 75%), hsl(from {$primaryColor} h s 70%));
}
CSS;
    }

    private function buildDarkModeBlock(
        string $darkmodePrimary,
        string $darkmodeSecondary,
        bool $secondaryIsDerived,
    ): string {
        $lines = [];

        if ('' !== $darkmodePrimary) {
            $lines[] = "    --token-color-primary-base: {$darkmodePrimary};";
        }

        if ('' !== $darkmodeSecondary) {
            $lines[] = "    --typo3-scaffold-header-bg: {$darkmodeSecondary};";
            $lines[] = "    --typo3-scaffold-sidebar-bg: {$darkmodeSecondary};";
        } elseif ('' !== $darkmodePrimary && $secondaryIsDerived) {
            $lines[] = '    --typo3-scaffold-header-bg: hsl(from var(--token-color-primary-base) h 20% 10%);';
            $lines[] = '    --typo3-scaffold-sidebar-bg: hsl(from var(--token-color-primary-base) h 20% 10%);';
        }

        if ([] === $lines) {
            return '';
        }

        $innerCss = implode("\n", $lines);

        return <<<CSS
html[data-color-scheme="dark"] {
{$innerCss}
}
CSS;
    }
}
