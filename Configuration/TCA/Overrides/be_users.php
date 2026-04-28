<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_backend_themes" TYPO3 CMS extension.
 *
 * (c) 2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use KonradMichalik\Typo3BackendThemes\Backend\Form\ItemsProcFunc\ThemeItemsProcFunc;

defined('TYPO3') || exit;

// Override the existing theme column definition to add custom themes.
// The field is already in the showitem from cms-backend, so we only
// replace the column config without calling addUserSetting().
$GLOBALS['TCA']['be_users']['columns']['user_settings']['columns']['theme'] = [
    'label' => 'backend.messages:theme',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['label' => '── Standard Themes ──', 'value' => '--div--'],
            ['label' => 'backend.messages:theme.fresh', 'value' => 'fresh'],
            ['label' => 'backend.messages:theme.modern', 'value' => 'modern'],
            ['label' => 'backend.messages:theme.classic', 'value' => 'classic'],
        ],
        'itemsProcFunc' => ThemeItemsProcFunc::class.'->addCustomThemes',
    ],
];
