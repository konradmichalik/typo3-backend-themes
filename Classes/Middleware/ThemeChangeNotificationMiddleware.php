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

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use TYPO3\CMS\Core\Messaging\{FlashMessage, FlashMessageService};
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function is_array;

/**
 * ThemeChangeNotificationMiddleware.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final readonly class ThemeChangeNotificationMiddleware implements MiddlewareInterface
{
    public function __construct(private FlashMessageService $flashMessageService) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ('POST' === $request->getMethod()) {
            $this->checkForThemeChange($request);
        }

        return $handler->handle($request);
    }

    private function checkForThemeChange(ServerRequestInterface $request): void
    {
        $postData = $request->getParsedBody();
        if (!is_array($postData)) {
            return;
        }

        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (null === $backendUser) {
            return;
        }

        $backendUserId = (int) ($backendUser->user['uid'] ?? 0);
        if (0 === $backendUserId) {
            return;
        }

        $submittedData = $postData['data']['be_users_settings'][$backendUserId] ?? [];
        $submittedTheme = $submittedData['user_settings__theme'] ?? null;

        if (null === $submittedTheme) {
            return;
        }

        $currentTheme = $backendUser->uc['theme'] ?? 'fresh';

        if ($submittedTheme === $currentTheme) {
            return;
        }

        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            'Please reload the page for the theme to take effect.',
            'Theme changed',
            ContextualFeedbackSeverity::INFO,
            true,
        );

        $this->flashMessageService
            ->getMessageQueueByIdentifier()
            ->enqueue($flashMessage);
    }
}
