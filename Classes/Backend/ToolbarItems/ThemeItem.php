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

namespace KonradMichalik\Typo3BackendThemes\Backend\ToolbarItems;

use KonradMichalik\Typo3BackendThemes\Service\{CssGenerator, ThemeService};
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Page\PageRenderer;


/**
 * ThemeItem.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */

final readonly class ThemeItem implements ToolbarItemInterface
{
    public function __construct(
        private PageRenderer $pageRenderer,
        private ThemeService $themeService,
        private CssGenerator $cssGenerator,
    ) {}

    public function checkAccess(): bool
    {
        return true;
    }

    public function getItem(): string
    {
        $this->pageRenderer->addJsInlineCode('backend_themes_reload_notice', <<<'JS'
            (function() {
                var form = document.querySelector('form[name="usersetup"]');
                if (!form) return;
                var sel = form.querySelector('select[name*="[theme]"]');
                if (!sel) return;
                var init = sel.value;
                sel.addEventListener('change', function() {
                    if (sel.value !== init && top.TYPO3 && top.TYPO3.Notification) {
                        top.TYPO3.Notification.info('Theme changed', 'Please save and reload the page for the theme to take effect.', 5);
                    }
                });
            })();
            JS);

        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (null === $backendUser) {
            return '';
        }

        $themeValue = (string) ($backendUser->uc['theme'] ?? '');

        if (!str_starts_with($themeValue, 'custom_')) {
            return '';
        }

        $uid = (int) substr($themeValue, 7);
        if ($uid <= 0) {
            return '';
        }

        $theme = $this->themeService->getThemeByUid($uid);
        if (null === $theme) {
            return '';
        }

        $css = $this->cssGenerator->generate($theme);

        if ('' === $css) {
            return '';
        }

        $this->pageRenderer->addCssInlineBlock('backend_themes', $css);

        return '';
    }

    public function hasDropDown(): bool
    {
        return false;
    }

    public function getDropDown(): string
    {
        return '';
    }

    /**
     * @return array<string, string>
     */
    public function getAdditionalAttributes(): array
    {
        return [];
    }

    public function getIndex(): int
    {
        return 0;
    }
}
