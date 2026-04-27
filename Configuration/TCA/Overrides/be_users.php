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
    'theme',
    [
        'label' => 'backend.messages:theme',
        'config' => [
            'type' => 'user',
            'renderType' => 'backendThemeSelect',
        ],
    ],
    'replace:theme',
);
