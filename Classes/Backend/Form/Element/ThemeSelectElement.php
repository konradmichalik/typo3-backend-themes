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
    /** @return array<string, mixed> */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        $themeService = GeneralUtility::makeInstance(ThemeService::class);
        $themes = $themeService->getAllThemes();

        $currentValue = (int)($this->data['parameterArray']['itemFormElValue'] ?? 0);
        $fieldName = $this->data['parameterArray']['itemFormElName'] ?? '';

        $options = '<option value="0"' . (0 === $currentValue ? ' selected' : '') . '>'
            . $this->getLanguageService()->sL('LLL:EXT:backend_themes/Resources/Private/Language/locallang.xlf:userSettings.backendTheme.standard')
            . '</option>';

        foreach ($themes as $theme) {
            $uid = (int)$theme['uid'];
            $selected = $uid === $currentValue ? ' selected' : '';
            $title = htmlspecialchars((string)$theme['title'], ENT_QUOTES, 'UTF-8');
            $options .= '<option value="' . $uid . '"' . $selected . '>' . $title . '</option>';
        }

        $resultArray['html'] = '<select class="form-select" name="' . htmlspecialchars($fieldName) . '">'
            . $options
            . '</select>';

        return $resultArray;
    }
}
