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

namespace KonradMichalik\Typo3BackendThemes\Tests\Unit\Backend\ToolbarItems;

use KonradMichalik\Typo3BackendThemes\Backend\ToolbarItems\ThemeItem;
use KonradMichalik\Typo3BackendThemes\Service\CssGenerator;
use KonradMichalik\Typo3BackendThemes\Service\ThemeService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Page\PageRenderer;

final class ThemeItemTest extends TestCase
{
    private PageRenderer&MockObject $pageRenderer;
    private ThemeService&MockObject $themeService;
    private CssGenerator&MockObject $cssGenerator;
    private BackendUserAuthentication&MockObject $backendUser;

    protected function setUp(): void
    {
        $this->pageRenderer = $this->createMock(PageRenderer::class);
        $this->themeService = $this->createMock(ThemeService::class);
        $this->cssGenerator = $this->createMock(CssGenerator::class);
        $this->backendUser = $this->createMock(BackendUserAuthentication::class);
    }

    private function createSubject(): ThemeItem
    {
        return new ThemeItem(
            $this->pageRenderer,
            $this->themeService,
            $this->cssGenerator,
            $this->backendUser,
        );
    }

    #[Test]
    public function getItemInjectsCssWhenUserHasTheme(): void
    {
        $theme = ['uid' => 1, 'primary_color' => '#3B82F6'];
        $css = ':root { --token-color-primary-base: #3B82F6; }';

        $this->backendUser->uc = ['backendTheme' => 1];

        $this->themeService->expects(self::once())
            ->method('getThemeByUid')
            ->with(1)
            ->willReturn($theme);

        $this->cssGenerator->expects(self::once())
            ->method('generate')
            ->with($theme)
            ->willReturn($css);

        $this->pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with('backend_themes', $css);

        $result = $this->createSubject()->getItem();

        self::assertSame('', $result);
    }

    #[Test]
    public function getItemFallsBackToDefaultTheme(): void
    {
        $theme = ['uid' => 2, 'primary_color' => '#1D4ED8'];
        $css = ':root { --token-color-primary-base: #1D4ED8; }';

        $this->backendUser->uc = [];

        $this->themeService->expects(self::never())
            ->method('getThemeByUid');

        $this->themeService->expects(self::once())
            ->method('getDefaultTheme')
            ->willReturn($theme);

        $this->cssGenerator->expects(self::once())
            ->method('generate')
            ->with($theme)
            ->willReturn($css);

        $this->pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with('backend_themes', $css);

        $result = $this->createSubject()->getItem();

        self::assertSame('', $result);
    }

    #[Test]
    public function getItemRendersNothingWhenNoThemeAvailable(): void
    {
        $this->backendUser->uc = [];

        $this->themeService->expects(self::once())
            ->method('getDefaultTheme')
            ->willReturn(null);

        $this->cssGenerator->expects(self::never())
            ->method('generate');

        $this->pageRenderer->expects(self::never())
            ->method('addCssInlineBlock');

        $result = $this->createSubject()->getItem();

        self::assertSame('', $result);
    }

    #[Test]
    public function getItemRendersNothingWhenCssIsEmpty(): void
    {
        $theme = ['uid' => 1, 'primary_color' => ''];

        $this->backendUser->uc = [];

        $this->themeService->expects(self::once())
            ->method('getDefaultTheme')
            ->willReturn($theme);

        $this->cssGenerator->expects(self::once())
            ->method('generate')
            ->with($theme)
            ->willReturn('');

        $this->pageRenderer->expects(self::never())
            ->method('addCssInlineBlock');

        $result = $this->createSubject()->getItem();

        self::assertSame('', $result);
    }

    #[Test]
    public function checkAccessReturnsTrue(): void
    {
        $this->backendUser->uc = [];

        self::assertTrue($this->createSubject()->checkAccess());
    }

    #[Test]
    public function hasDropDownReturnsFalse(): void
    {
        $this->backendUser->uc = [];

        self::assertFalse($this->createSubject()->hasDropDown());
    }
}
