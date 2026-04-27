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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;

final readonly class DataHandlerHook
{
    private const TABLE_NAME = 'tx_backendthemes_theme';

    public function __construct(private ConnectionPool $connectionPool) {}

    /** @param array<string, mixed> $fieldArray */
    public function processDatamap_afterDatabaseOperations(
        string $status,
        string $table,
        int|string $id,
        array &$fieldArray,
        DataHandler $dataHandler,
    ): void {
        if ($table !== self::TABLE_NAME) {
            return;
        }

        if (!isset($fieldArray['is_default']) || (int)$fieldArray['is_default'] !== 1) {
            return;
        }

        if ('new' === $status) {
            $id = $dataHandler->substNEWwithIDs[$id] ?? 0;
            if (0 === (int)$id) {
                return;
            }
        }

        $uid = (int)$id;
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $queryBuilder
            ->update(self::TABLE_NAME)
            ->set('is_default', 0)
            ->where(
                $queryBuilder->expr()->eq('is_default', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)),
                $queryBuilder->expr()->neq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
            )
            ->executeStatement();
    }
}
