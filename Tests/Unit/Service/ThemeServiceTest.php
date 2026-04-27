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

namespace KonradMichalik\Typo3BackendThemes\Tests\Unit\Service;

use Doctrine\DBAL\Result;
use KonradMichalik\Typo3BackendThemes\Service\ThemeService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

final class ThemeServiceTest extends TestCase
{
    /**
     * @param array<string, mixed>|false $result
     */
    private function createQueryBuilderMock(array|false $result): QueryBuilder&MockObject
    {
        $resultMock = $this->createMock(Result::class);
        $resultMock->method('fetchAssociative')->willReturn($result);

        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->method('select')->willReturnSelf();
        $queryBuilderMock->method('from')->willReturnSelf();
        $queryBuilderMock->method('where')->willReturnSelf();
        $queryBuilderMock->method('setMaxResults')->willReturnSelf();
        $queryBuilderMock->method('orderBy')->willReturnSelf();
        $queryBuilderMock->method('createNamedParameter')->willReturn(':dcValue1');
        $queryBuilderMock->method('expr')->willReturn(
            $this->createMock(\TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder::class)
        );
        $queryBuilderMock->method('executeQuery')->willReturn($resultMock);

        return $queryBuilderMock;
    }

    /**
     * @param array<int, array<string, mixed>> $results
     */
    private function createQueryBuilderMockForAll(array $results): QueryBuilder&MockObject
    {
        $resultMock = $this->createMock(Result::class);
        $resultMock->method('fetchAllAssociative')->willReturn($results);

        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->method('select')->willReturnSelf();
        $queryBuilderMock->method('from')->willReturnSelf();
        $queryBuilderMock->method('where')->willReturnSelf();
        $queryBuilderMock->method('setMaxResults')->willReturnSelf();
        $queryBuilderMock->method('orderBy')->willReturnSelf();
        $queryBuilderMock->method('createNamedParameter')->willReturn(':dcValue1');
        $queryBuilderMock->method('expr')->willReturn(
            $this->createMock(\TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder::class)
        );
        $queryBuilderMock->method('executeQuery')->willReturn($resultMock);

        return $queryBuilderMock;
    }

    private function createConnectionPoolMock(QueryBuilder&MockObject $queryBuilder): ConnectionPool&MockObject
    {
        $connectionPoolMock = $this->createMock(ConnectionPool::class);
        $connectionPoolMock->method('getQueryBuilderForTable')->willReturn($queryBuilder);

        return $connectionPoolMock;
    }

    #[Test]
    public function getDefaultThemeReturnsThemeWithIsDefaultFlag(): void
    {
        $themeRecord = [
            'uid' => 1,
            'title' => 'Default Theme',
            'is_default' => 1,
            'primary_color' => '#3B82F6',
        ];

        $queryBuilderMock = $this->createQueryBuilderMock($themeRecord);
        $connectionPoolMock = $this->createConnectionPoolMock($queryBuilderMock);

        $subject = new ThemeService($connectionPoolMock);

        $result = $subject->getDefaultTheme();

        self::assertSame($themeRecord, $result);
    }

    #[Test]
    public function getDefaultThemeReturnsNullWhenNoDefault(): void
    {
        $queryBuilderMock = $this->createQueryBuilderMock(false);
        $connectionPoolMock = $this->createConnectionPoolMock($queryBuilderMock);

        $subject = new ThemeService($connectionPoolMock);

        $result = $subject->getDefaultTheme();

        self::assertNull($result);
    }

    #[Test]
    public function getThemeByUidReturnsThemeRecord(): void
    {
        $themeRecord = [
            'uid' => 2,
            'title' => 'Blue Theme',
            'is_default' => 0,
            'primary_color' => '#1D4ED8',
        ];

        $queryBuilderMock = $this->createQueryBuilderMock($themeRecord);
        $connectionPoolMock = $this->createConnectionPoolMock($queryBuilderMock);

        $subject = new ThemeService($connectionPoolMock);

        $result = $subject->getThemeByUid(2);

        self::assertSame($themeRecord, $result);
    }

    #[Test]
    public function getThemeByUidReturnsNullForNonExistentTheme(): void
    {
        $queryBuilderMock = $this->createQueryBuilderMock(false);
        $connectionPoolMock = $this->createConnectionPoolMock($queryBuilderMock);

        $subject = new ThemeService($connectionPoolMock);

        $result = $subject->getThemeByUid(999);

        self::assertNull($result);
    }

    #[Test]
    public function getAllThemesReturnsAllNonHiddenRecords(): void
    {
        $themes = [
            ['uid' => 1, 'title' => 'Alpha Theme', 'primary_color' => '#3B82F6'],
            ['uid' => 2, 'title' => 'Beta Theme', 'primary_color' => '#1D4ED8'],
            ['uid' => 3, 'title' => 'Gamma Theme', 'primary_color' => '#7C3AED'],
        ];

        $queryBuilderMock = $this->createQueryBuilderMockForAll($themes);
        $connectionPoolMock = $this->createConnectionPoolMock($queryBuilderMock);

        $subject = new ThemeService($connectionPoolMock);

        $result = $subject->getAllThemes();

        self::assertSame($themes, $result);
        self::assertCount(3, $result);
    }
}
