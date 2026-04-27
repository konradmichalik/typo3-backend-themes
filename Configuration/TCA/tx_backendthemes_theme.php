<?php

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
        'iconIdentifier' => 'backend-themes-record',
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
                    --palette--;;colors,
                --div--;LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:palette.darkmode,
                    --palette--;;darkmode,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden
            ',
        ],
    ],
    'palettes' => [
        'general' => [
            'showitem' => 'title, is_default',
        ],
        'colors' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:palette.colors',
            'showitem' => 'primary_color, --linebreak--, auto_secondary, secondary_color',
        ],
        'darkmode' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:palette.darkmode',
            'showitem' => 'darkmode_primary_color, darkmode_secondary_color',
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
                'fieldWizard' => [
                    'backendThemePreview' => [
                        'renderType' => 'backendThemePreview',
                    ],
                ],
            ],
        ],
        'secondary_color' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.secondary_color',
            'displayCond' => 'FIELD:auto_secondary:=:0',
            'config' => [
                'type' => 'color',
            ],
        ],
        'auto_secondary' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.auto_secondary',
            'onChange' => 'reload',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 1,
                'items' => [
                    [
                        'label' => '',
                    ],
                ],
            ],
        ],
        'darkmode_primary_color' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.darkmode_primary_color',
            'config' => [
                'type' => 'color',
            ],
        ],
        'darkmode_secondary_color' => [
            'label' => 'LLL:EXT:backend_themes/Resources/Private/Language/locallang_tca.xlf:tx_backendthemes_theme.darkmode_secondary_color',
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
            ],
        ],
    ],
];
