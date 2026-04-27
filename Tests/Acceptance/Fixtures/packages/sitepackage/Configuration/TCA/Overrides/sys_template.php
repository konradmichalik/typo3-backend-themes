<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || exit('Access denied.');

ExtensionManagementUtility::addStaticFile(
    'sitepackage',
    'Configuration/TypoScript',
    'Sitepackage',
);
