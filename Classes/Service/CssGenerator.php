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
        $primaryColor = (string)($theme['primary_color'] ?? '');

        if (!$this->isValidHexColor($primaryColor)) {
            return '';
        }

        $autoSecondary = (bool)($theme['auto_secondary'] ?? true);
        $secondaryColor = (string)($theme['secondary_color'] ?? '');
        if ('' !== $secondaryColor && !$this->isValidHexColor($secondaryColor)) {
            $secondaryColor = '';
        }
        $darkmodePrimaryColor = (string)($theme['darkmode_primary_color'] ?? '');
        if ('' !== $darkmodePrimaryColor && !$this->isValidHexColor($darkmodePrimaryColor)) {
            $darkmodePrimaryColor = '';
        }
        $darkmodeSecondaryColor = (string)($theme['darkmode_secondary_color'] ?? '');
        if ('' !== $darkmodeSecondaryColor && !$this->isValidHexColor($darkmodeSecondaryColor)) {
            $darkmodeSecondaryColor = '';
        }

        $scaffoldBg = $this->resolveScaffoldBg($autoSecondary, $secondaryColor);

        $css = $this->buildRootBlock($primaryColor, $scaffoldBg);
        $css .= "\n";
        $css .= $this->buildIconAccentBlock();

        if ($darkmodePrimaryColor !== '' || $darkmodeSecondaryColor !== '') {
            $css .= "\n";
            $css .= $this->buildDarkModeBlock(
                $darkmodePrimaryColor,
                $darkmodeSecondaryColor,
                $autoSecondary
            );
        }

        return $css;
    }

    private function isValidHexColor(string $color): bool
    {
        return preg_match(self::HEX_COLOR_PATTERN, $color) === 1;
    }

    private function resolveScaffoldBg(bool $autoSecondary, string $secondaryColor): string
    {
        if ($autoSecondary) {
            return 'light-dark(hsl(from var(--token-color-primary-base) h 40% 20%), hsl(from var(--token-color-primary-base) h 20% 10%))';
        }

        return $secondaryColor;
    }

    private function buildRootBlock(string $primaryColor, string $scaffoldBg): string
    {
        return <<<CSS
:root {
    --token-color-primary-base: {$primaryColor};
    --typo3-scaffold-header-color: var(--typo3-surface-primary-text);
    --typo3-scaffold-header-bg: {$scaffoldBg};
    --typo3-scaffold-header-box-shadow: none;
    --typo3-scaffold-sidebar-color: var(--typo3-surface-primary-text);
    --typo3-scaffold-sidebar-bg: {$scaffoldBg};
    --typo3-scaffold-sidebar-border-width: 0;
}
CSS;
    }

    private function buildIconAccentBlock(): string
    {
        return <<<CSS
.scaffold-sidebar {
    --typo3-icons-accent: light-dark(hsl(from var(--token-color-primary-base) h s 75%), hsl(from var(--token-color-primary-base) h s 70%));
}
CSS;
    }

    private function buildDarkModeBlock(
        string $darkmodePrimaryColor,
        string $darkmodeSecondaryColor,
        bool $autoSecondary
    ): string {
        $lines = [];

        if ($darkmodePrimaryColor !== '') {
            $lines[] = "    --token-color-primary-base: {$darkmodePrimaryColor};";
        }

        $darkScaffoldBg = $this->resolveDarkScaffoldBg(
            $darkmodePrimaryColor,
            $darkmodeSecondaryColor,
            $autoSecondary
        );

        if ($darkScaffoldBg !== '') {
            $lines[] = "    --typo3-scaffold-header-bg: {$darkScaffoldBg};";
            $lines[] = "    --typo3-scaffold-sidebar-bg: {$darkScaffoldBg};";
        }

        $innerCss = implode("\n", $lines);

        return <<<CSS
[data-color-scheme="dark"] {
{$innerCss}
}
CSS;
    }

    private function resolveDarkScaffoldBg(
        string $darkmodePrimaryColor,
        string $darkmodeSecondaryColor,
        bool $autoSecondary
    ): string {
        if ($darkmodeSecondaryColor !== '') {
            return $darkmodeSecondaryColor;
        }

        if ($darkmodePrimaryColor !== '' && $autoSecondary) {
            return 'hsl(from var(--token-color-primary-base) h 20% 10%)';
        }

        return '';
    }
}
