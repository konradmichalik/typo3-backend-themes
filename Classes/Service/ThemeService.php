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

namespace KonradMichalik\Typo3BackendThemes\Service;

use TYPO3\CMS\Core\Database\{Connection, ConnectionPool};

/**
 * ThemeService.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final readonly class ThemeService
{
    private const TABLE_NAME = 'tx_backendthemes_theme';

    public function __construct(private ConnectionPool $connectionPool) {}

    /**
     * @return array<string, mixed>|null
     */
    public function getDefaultTheme(): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'is_default',
                    $queryBuilder->createNamedParameter(1, Connection::PARAM_INT),
                ),
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return false !== $result ? $result : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getThemeByUid(int $uid): ?array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT),
                ),
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return false !== $result ? $result : null;
    }

    /**
     * Resolve active theme for the current backend user.
     * Returns null if the user has a standard theme selected.
     *
     * @return array<string, mixed>|null
     */
    public function resolveUserTheme(): ?array
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (null === $backendUser) {
            return null;
        }

        $themeValue = (string) ($backendUser->uc['theme'] ?? '');

        if (!str_starts_with($themeValue, 'custom_')) {
            return null;
        }

        $uid = (int) substr($themeValue, 7);

        return $uid > 0 ? $this->getThemeByUid($uid) : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllThemes(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        return $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->orderBy('title')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
