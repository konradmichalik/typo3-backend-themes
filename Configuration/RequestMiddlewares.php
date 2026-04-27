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
    'backend' => [
        'konradmichalik/backend-themes/theme-change-notification' => [
            'target' => \KonradMichalik\Typo3BackendThemes\Middleware\ThemeChangeNotificationMiddleware::class,
            'before' => [
                'typo3/cms-backend/output-compression',
            ],
            'after' => [
                'typo3/cms-backend/authentication',
            ],
        ],
    ],
];
