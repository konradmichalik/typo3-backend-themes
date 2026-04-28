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

defined('TYPO3') || exit;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1745740800] = [
    'nodeName' => 'backendThemePreview',
    'priority' => 40,
    'class' => KonradMichalik\Typo3BackendThemes\Backend\Form\Element\ThemePreviewElement::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['typo3_backend_themes']
    = KonradMichalik\Typo3BackendThemes\Hook\DataHandlerHook::class;
