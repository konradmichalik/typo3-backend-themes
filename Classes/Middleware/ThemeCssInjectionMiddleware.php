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

namespace KonradMichalik\Typo3BackendThemes\Middleware;

use KonradMichalik\Typo3BackendThemes\Service\CssGenerator;
use KonradMichalik\Typo3BackendThemes\Service\ThemeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Page\PageRenderer;


/**
 * ThemeCssInjectionMiddleware.
 *
 * Injects custom theme CSS into every backend response, including
 * the list_frame iframe that renders module content.
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
        $this->injectThemeCss();

        return $handler->handle($request);
    }

    private function injectThemeCss(): void
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (null === $backendUser) {
            return;
        }

        $themeValue = (string) ($backendUser->uc['theme'] ?? '');

        if (!str_starts_with($themeValue, 'custom_')) {
            return;
        }

        $uid = (int) substr($themeValue, 7);
        if ($uid <= 0) {
            return;
        }

        $theme = $this->themeService->getThemeByUid($uid);
        if (null === $theme) {
            return;
        }

        $css = $this->cssGenerator->generate($theme);
        if ('' === $css) {
            return;
        }

        $this->pageRenderer->addCssInlineBlock('backend_themes', $css);
    }
}
