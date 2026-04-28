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

namespace KonradMichalik\Typo3BackendThemes\Hook;

use TYPO3\CMS\Core\Database\{Connection, ConnectionPool};
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\{FlashMessage, FlashMessageService};
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DataHandlerHook.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final readonly class DataHandlerHook
{
    private const TABLE_NAME = 'tx_backendthemes_theme';

    public function __construct(private ConnectionPool $connectionPool, private readonly FlashMessageService $flashMessageService) {}

    /** @param array<string, mixed> $fieldArray */
    public function processDatamap_afterDatabaseOperations(
        string $status,
        string $table,
        int|string $id,
        array &$fieldArray,
        DataHandler $dataHandler,
    ): void {
        if (self::TABLE_NAME !== $table) {
            return;
        }

        $this->enforceSingleDefault($status, $id, $fieldArray, $dataHandler);
        $this->addReloadMessage();
    }

    private function enforceSingleDefault(
        string $status,
        int|string $id,
        array &$fieldArray,
        DataHandler $dataHandler,
    ): void {
        if (!isset($fieldArray['is_default']) || 1 !== (int) $fieldArray['is_default']) {
            return;
        }

        if ('new' === $status) {
            $id = $dataHandler->substNEWwithIDs[$id] ?? 0;
            if (0 === (int) $id) {
                return;
            }
        }

        $uid = (int) $id;
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $queryBuilder
            ->update(self::TABLE_NAME)
            ->set('is_default', 0)
            ->where(
                $queryBuilder->expr()->eq('is_default', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)),
            )
            ->executeStatement();
    }

    private function addReloadMessage(): void
    {
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            'Please reload the page for the theme changes to take effect.',
            'Theme updated',
            ContextualFeedbackSeverity::INFO,
            true,
        );

        $this->flashMessageService
            ->getMessageQueueByIdentifier()
            ->enqueue($flashMessage);
    }
}
