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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || exit;

ExtensionManagementUtility::addUserSetting(
    label: 'LLL:EXT:backend_themes/Resources/Private/Language/locallang.xlf:userSettings.backendTheme',
    field: 'backendTheme',
    config: [
        'type' => 'user',
        'renderType' => 'backendThemeSelect',
        'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang.xlf:userSettings.backendTheme',
    ],
    after: 'theme',
);
