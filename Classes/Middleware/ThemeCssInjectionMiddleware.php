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

namespace KonradMichalik\Typo3BackendThemes\Middleware;

use KonradMichalik\Typo3BackendThemes\Service\{CssGenerator, ThemeService};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * ThemeCssInjectionMiddleware.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final readonly class ThemeCssInjectionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private PageRenderer $pageRenderer,
        private ThemeService $themeService,
        private CssGenerator $cssGenerator,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $theme = $this->themeService->resolveUserTheme();

        if (null !== $theme) {
            $css = $this->cssGenerator->generate($theme);
            if ('' !== $css) {
                $this->pageRenderer->addCssInlineBlock('typo3_backend_themes', $css);
            }
        }

        return $handler->handle($request);
    }
}
