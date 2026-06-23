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

namespace KonradMichalik\Typo3BackendThemes\Controller;

use KonradMichalik\Typo3BackendThemes\Service\{CssGenerator, ThemeService};
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

/**
 * CssController.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final readonly class CssController
{
    public function __construct(
        private ThemeService $themeService,
        private CssGenerator $cssGenerator,
    ) {}

    public function cssAction(ServerRequestInterface $request): JsonResponse
    {
        $theme = $this->themeService->resolveUserTheme();
        $css = null !== $theme ? $this->cssGenerator->generate($theme) : '';

        return new JsonResponse(['css' => $css]);
    }
}
