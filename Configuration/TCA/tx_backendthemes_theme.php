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

return [
    'ctrl' => [
        'title' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme',
        'label' => 'title',
        'rootLevel' => 1,
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'typeicon_classes' => [
            'default' => 'backend-themes-record',
        ],
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;general,
                    primary_color,
                    --palette--;;overrides,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden
            ',
        ],
    ],
    'palettes' => [
        'general' => [
            'showitem' => 'title, is_default',
        ],
        'overrides' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:palette.overrides',
            'showitem' => 'secondary_color, --linebreak--, darkmode_primary_color, darkmode_secondary_color',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'primary_color' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.primary_color',
            'config' => [
                'type' => 'color',
                'required' => true,
            ],
        ],
        'secondary_color' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.secondary_color',
            'description' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.secondary_color.description',
            'config' => [
                'type' => 'color',
            ],
        ],
        'darkmode_primary_color' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.darkmode_primary_color',
            'description' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.darkmode_primary_color.description',
            'config' => [
                'type' => 'color',
            ],
        ],
        'darkmode_secondary_color' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.darkmode_secondary_color',
            'description' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.darkmode_secondary_color.description',
            'config' => [
                'type' => 'color',
            ],
        ],
        'is_default' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.is_default',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        'label' => '',
                    ],
                ],
                'fieldWizard' => [
                    'backendThemePreview' => [
                        'renderType' => 'backendThemePreview',
                    ],
                ],
            ],
        ],
    ],
];
