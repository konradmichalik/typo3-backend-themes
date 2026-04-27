<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Backend Themes',
    'description' => 'Configure and select custom backend color themes with primary/secondary colors, dark mode support and live preview.',
    'category' => 'be',
    'author' => 'Konrad Michalik',
    'author_email' => 'hej@konradmichalik.dev',
    'state' => 'beta',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.5.99',
            'typo3' => '14.0.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
