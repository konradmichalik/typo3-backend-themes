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

namespace KonradMichalik\Typo3BackendThemes\Backend\Form\ItemsProcFunc;

use KonradMichalik\Typo3BackendThemes\Service\ThemeService;


/**
 * ThemeItemsProcFunc.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */

final readonly class ThemeItemsProcFunc
{
    public function __construct(
        private ThemeService $themeService,
    ) {}

    /**
     * @param array<string, mixed> $params
     */
    public function addCustomThemes(array &$params): void
    {
        $customThemes = $this->themeService->getAllThemes();

        if ([] === $customThemes) {
            return;
        }

        // Find the default theme and place it at the very top of the list
        $defaultTheme = null;
        $otherThemes = [];
        foreach ($customThemes as $theme) {
            if ((int) $theme['is_default']) {
                $defaultTheme = $theme;
            } else {
                $otherThemes[] = $theme;
            }
        }

        if (null !== $defaultTheme) {
            // Insert default theme before all standard themes
            array_unshift($params['items'], [
                'label' => (string) $defaultTheme['title'].' (Default)',
                'value' => 'custom_'.(int) $defaultTheme['uid'],
            ]);
        }

        if ([] !== $otherThemes) {
            $params['items'][] = [
                'label' => '── Custom Themes ──',
                'value' => '--div--',
            ];

            foreach ($otherThemes as $theme) {
                $params['items'][] = [
                    'label' => (string) $theme['title'],
                    'value' => 'custom_'.(int) $theme['uid'],
                ];
            }
        }
    }
}
