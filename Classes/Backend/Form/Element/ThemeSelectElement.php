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

namespace KonradMichalik\Typo3BackendThemes\Backend\Form\Element;

use KonradMichalik\Typo3BackendThemes\Service\ThemeService;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ThemeSelectElement extends AbstractFormElement
{
    private const DEFAULT_THEMES = [
        'fresh' => 'backend.messages:theme.fresh',
        'modern' => 'backend.messages:theme.modern',
        'classic' => 'backend.messages:theme.classic',
    ];

    /** @return array<string, mixed> */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $parameterArray = $this->data['parameterArray'] ?? [];
        $currentValue = (string)($parameterArray['itemFormElValue'] ?? 'fresh');
        $fieldName = (string)($parameterArray['itemFormElName'] ?? '');

        $themeService = GeneralUtility::getContainer()->get(ThemeService::class);
        $customThemes = $themeService->getAllThemes();

        $lang = $this->getLanguageService();

        $options = '<optgroup label="' . htmlspecialchars($lang->sL('LLL:EXT:backend_themes/Resources/Private/Language/locallang.xlf:userSettings.optgroup.default')) . '">';
        foreach (self::DEFAULT_THEMES as $value => $labelKey) {
            $selected = $value === $currentValue ? ' selected' : '';
            $label = htmlspecialchars($lang->sL($labelKey));
            $options .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
        }
        $options .= '</optgroup>';

        if ($customThemes !== []) {
            $options .= '<optgroup label="' . htmlspecialchars($lang->sL('LLL:EXT:backend_themes/Resources/Private/Language/locallang.xlf:userSettings.optgroup.custom')) . '">';
            foreach ($customThemes as $theme) {
                $value = 'custom_' . (int)$theme['uid'];
                $selected = $value === $currentValue ? ' selected' : '';
                $title = htmlspecialchars((string)$theme['title'], ENT_QUOTES, 'UTF-8');
                $options .= '<option value="' . $value . '"' . $selected . '>' . $title . '</option>';
            }
            $options .= '</optgroup>';
        }

        $resultArray['html'] = '<select class="form-select" name="' . htmlspecialchars($fieldName) . '">'
            . $options
            . '</select>';

        return $resultArray;
    }
}
