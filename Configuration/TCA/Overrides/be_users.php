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

use KonradMichalik\Typo3BackendThemes\Backend\Form\ItemsProcFunc\ThemeItemsProcFunc;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || exit;

ExtensionManagementUtility::addUserSetting(
    'theme',
    [
        'label' => 'backend.messages:theme',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => 'backend.messages:theme.fresh', 'value' => 'fresh'],
                ['label' => 'backend.messages:theme.modern', 'value' => 'modern'],
                ['label' => 'backend.messages:theme.classic', 'value' => 'classic'],
            ],
            'itemsProcFunc' => ThemeItemsProcFunc::class . '->addCustomThemes',
        ],
    ],
    'replace:theme',
);
