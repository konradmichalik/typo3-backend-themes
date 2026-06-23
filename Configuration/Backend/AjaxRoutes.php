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

use KonradMichalik\Typo3BackendThemes\Controller\CssController;

return [
    'backend_themes_css' => [
        'path' => '/backend-themes/css',
        'target' => CssController::class.'::cssAction',
    ],
];
