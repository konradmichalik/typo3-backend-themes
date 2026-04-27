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

namespace KonradMichalik\Typo3BackendThemes\Tests\Unit\Hook;

use KonradMichalik\Typo3BackendThemes\Hook\DataHandlerHook;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;


/**
 * DataHandlerHookTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */

final class DataHandlerHookTest extends TestCase
{
    private ConnectionPool&MockObject $connectionPool;
    private DataHandlerHook $subject;

    protected function setUp(): void
    {
        $this->connectionPool = $this->createMock(ConnectionPool::class);
        $this->subject = new DataHandlerHook($this->connectionPool);
    }

    #[Test]
    public function afterDatabaseOperationsResetsOtherDefaultsWhenSettingDefault(): void
    {
        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder->method('eq')->willReturn('is_default = 1');
        $expressionBuilder->method('neq')->willReturn('uid != 5');

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('update')->willReturnSelf();
        $queryBuilder->method('set')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('expr')->willReturn($expressionBuilder);
        $queryBuilder->method('createNamedParameter')->willReturn(':dcValue1');
        $queryBuilder->expects(self::once())->method('executeStatement');

        $this->connectionPool
            ->expects(self::once())
            ->method('getQueryBuilderForTable')
            ->with('tx_backendthemes_theme')
            ->willReturn($queryBuilder);

        $dataHandler = $this->createMock(DataHandler::class);
        $fieldArray = ['is_default' => 1];

        $this->subject->processDatamap_afterDatabaseOperations(
            'update',
            'tx_backendthemes_theme',
            5,
            $fieldArray,
            $dataHandler,
        );
    }

    #[Test]
    public function afterDatabaseOperationsSkipsWhenNotSettingDefault(): void
    {
        $this->connectionPool
            ->expects(self::never())
            ->method('getQueryBuilderForTable');

        $dataHandler = $this->createMock(DataHandler::class);
        $fieldArray = ['title' => 'My Theme'];

        $this->subject->processDatamap_afterDatabaseOperations(
            'update',
            'tx_backendthemes_theme',
            5,
            $fieldArray,
            $dataHandler,
        );
    }

    #[Test]
    public function afterDatabaseOperationsSkipsForOtherTables(): void
    {
        $this->connectionPool
            ->expects(self::never())
            ->method('getQueryBuilderForTable');

        $dataHandler = $this->createMock(DataHandler::class);
        $fieldArray = ['is_default' => 1];

        $this->subject->processDatamap_afterDatabaseOperations(
            'update',
            'pages',
            5,
            $fieldArray,
            $dataHandler,
        );
    }
}
