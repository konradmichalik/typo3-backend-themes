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

namespace KonradMichalik\Typo3BackendThemes\Tests\Unit\Service;

use KonradMichalik\Typo3BackendThemes\Service\CssGenerator;
use PHPUnit\Framework\Attributes\{DataProvider, Test};
use PHPUnit\Framework\TestCase;

/**
 * CssGeneratorTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class CssGeneratorTest extends TestCase
{
    private CssGenerator $subject;

    protected function setUp(): void
    {
        $this->subject = new CssGenerator();
    }

    #[Test]
    public function generateReturnsPrimaryColorVariable(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'header_color' => '',
            'sidebar_color' => '',
            'darkmode_primary_color' => '',
            'darkmode_header_color' => '',
            'darkmode_sidebar_color' => '',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringContainsString('--token-color-primary-base: #3B82F6;', $css);
    }

    #[Test]
    public function generateWithEmptySecondaryUsesHslDerivedColors(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'header_color' => '',
            'sidebar_color' => '',
            'darkmode_primary_color' => '',
            'darkmode_header_color' => '',
            'darkmode_sidebar_color' => '',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringContainsString(
            'light-dark(hsl(from var(--token-color-primary-base) h 40% 20%), hsl(from var(--token-color-primary-base) h 20% 10%))',
            $css,
        );
        self::assertStringContainsString('--typo3-scaffold-header-bg:', $css);
        self::assertStringContainsString('--typo3-scaffold-sidebar-bg:', $css);
    }

    #[Test]
    public function generateWithExplicitSecondaryUsesDirectColor(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'header_color' => '#1E3A5F',
            'sidebar_color' => '#1E3A5F',
            'darkmode_primary_color' => '',
            'darkmode_header_color' => '',
            'darkmode_sidebar_color' => '',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringContainsString('--typo3-scaffold-header-bg: #1E3A5F;', $css);
        self::assertStringContainsString('--typo3-scaffold-sidebar-bg: #1E3A5F;', $css);
        self::assertStringNotContainsString(
            'light-dark(hsl(from var(--token-color-primary-base) h 40% 20%)',
            $css,
        );
    }

    #[Test]
    public function generateWithDarkmodeOverrideCreatesDarkModeBlock(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'secondary_color' => '',
            'darkmode_primary_color' => '#1D4ED8',
            'darkmode_secondary_color' => '',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringContainsString('[data-color-scheme="dark"]', $css);
        self::assertStringContainsString('--token-color-primary-base: #1D4ED8;', $css);
    }

    #[Test]
    public function generateWithoutDarkmodeOverrideOmitsDarkModeBlock(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'header_color' => '',
            'sidebar_color' => '',
            'darkmode_primary_color' => '',
            'darkmode_header_color' => '',
            'darkmode_sidebar_color' => '',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringNotContainsString('[data-color-scheme="dark"]', $css);
    }

    #[Test]
    public function generateWithExplicitDarkmodeSecondaryUsesDirectColor(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'header_color' => '#1E3A5F',
            'sidebar_color' => '#1E3A5F',
            'darkmode_primary_color' => '#1D4ED8',
            'darkmode_header_color' => '#0F2A4A',
            'darkmode_sidebar_color' => '#0F2A4A',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringContainsString('[data-color-scheme="dark"]', $css);
        // Dark mode block must contain the explicit dark secondary color
        $darkBlockStart = strpos($css, '[data-color-scheme="dark"]');
        self::assertNotFalse($darkBlockStart);
        $darkBlock = substr($css, $darkBlockStart);
        self::assertStringContainsString('--typo3-scaffold-header-bg: #0F2A4A;', $darkBlock);
        self::assertStringContainsString('--typo3-scaffold-sidebar-bg: #0F2A4A;', $darkBlock);
    }

    #[Test]
    public function generateIncludesIconAccentVariable(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'header_color' => '',
            'sidebar_color' => '',
            'darkmode_primary_color' => '',
            'darkmode_header_color' => '',
            'darkmode_sidebar_color' => '',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringContainsString('--typo3-icons-accent:', $css);
        self::assertStringContainsString('.scaffold-sidebar', $css);
    }

    #[Test]
    public function generateIncludesHeaderAndSidebarTextColor(): void
    {
        $theme = [
            'primary_color' => '#3B82F6',
            'header_color' => '',
            'sidebar_color' => '',
            'darkmode_primary_color' => '',
            'darkmode_header_color' => '',
            'darkmode_sidebar_color' => '',
        ];

        $css = $this->subject->generate($theme);

        self::assertStringContainsString('--typo3-scaffold-header-color: var(--typo3-surface-primary-text);', $css);
        self::assertStringContainsString('--typo3-scaffold-sidebar-color: var(--typo3-surface-primary-text);', $css);
    }

    /**
     * @return array<string, array{0: array<string, mixed>}>
     */
    public static function invalidPrimaryColorProvider(): array
    {
        return [
            'empty string' => [
                ['primary_color' => '', 'header_color' => '', 'sidebar_color' => '', 'darkmode_primary_color' => '', 'darkmode_header_color' => '', 'darkmode_sidebar_color' => ''],
            ],
            'no hash prefix' => [
                ['primary_color' => '3B82F6', 'header_color' => '', 'sidebar_color' => '', 'darkmode_primary_color' => '', 'darkmode_header_color' => '', 'darkmode_sidebar_color' => ''],
            ],
            'invalid hex characters' => [
                ['primary_color' => '#ZZZZZZ', 'header_color' => '', 'sidebar_color' => '', 'darkmode_primary_color' => '', 'darkmode_header_color' => '', 'darkmode_sidebar_color' => ''],
            ],
            'too short 3-char hex' => [
                ['primary_color' => '#F0F', 'header_color' => '', 'sidebar_color' => '', 'darkmode_primary_color' => '', 'darkmode_header_color' => '', 'darkmode_sidebar_color' => ''],
            ],
            'rgb format' => [
                ['primary_color' => 'rgb(59, 130, 246)', 'header_color' => '', 'sidebar_color' => '', 'darkmode_primary_color' => '', 'darkmode_header_color' => '', 'darkmode_sidebar_color' => ''],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $theme
     */
    #[DataProvider('invalidPrimaryColorProvider')]
    #[Test]
    public function generateReturnsEmptyStringForInvalidPrimaryColor(array $theme): void
    {
        $css = $this->subject->generate($theme);

        self::assertSame('', $css);
    }
}
