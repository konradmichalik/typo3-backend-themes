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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * ThemeChangeNotificationMiddleware.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */

final class ThemeChangeNotificationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $themeBefore = $GLOBALS['BE_USER']->uc['theme'] ?? null;
        $response = $handler->handle($request);
        $themeAfter = $GLOBALS['BE_USER']->uc['theme'] ?? null;

        if (null !== $themeBefore && $themeAfter !== $themeBefore) {
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                'Please reload the page for the theme to take effect.',
                'Theme changed',
                ContextualFeedbackSeverity::INFO,
                true,
            );

            GeneralUtility::makeInstance(FlashMessageService::class)
                ->getMessageQueueByIdentifier()
                ->enqueue($flashMessage);
        }

        return $response;
    }
}
