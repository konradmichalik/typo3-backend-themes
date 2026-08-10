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

namespace KonradMichalik\Typo3BackendThemes\Tests\Unit\Controller;

use Doctrine\DBAL\Result;
use KonradMichalik\Ttt\Attribute\WithBackendUser;
use KonradMichalik\Typo3BackendThemes\Controller\CssController;
use KonradMichalik\Typo3BackendThemes\Service\{CssGenerator, ThemeService};
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * CssControllerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class CssControllerTest extends TestCase
{
    #[Test]
    public function cssActionReturnsEmptyCssWhenNoThemeResolved(): void
    {
        // No backend user is registered, so no theme can be resolved.
        $subject = new CssController(
            new ThemeService($this->createMock(ConnectionPool::class)),
            new CssGenerator(),
        );

        $response = $subject->cssAction($this->createMock(ServerRequestInterface::class));
        $payload = $this->decodeBody($response->getBody());

        self::assertSame('', $payload['css']);
    }

    #[Test]
    #[WithBackendUser]
    public function cssActionReturnsGeneratedCssForResolvedTheme(): void
    {
        $GLOBALS['BE_USER']->uc = ['theme' => 'custom_1'];

        $themeRecord = ['uid' => 1, 'title' => 'Blue', 'primary_color' => '#3B82F6'];
        $connectionPool = $this->createConnectionPoolMock($themeRecord);

        $subject = new CssController(
            new ThemeService($connectionPool),
            new CssGenerator(),
        );

        $response = $subject->cssAction($this->createMock(ServerRequestInterface::class));
        $payload = $this->decodeBody($response->getBody());

        self::assertStringContainsString('--token-color-primary-base: #3b82f6;', $payload['css']);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeBody(\Psr\Http\Message\StreamInterface $body): array
    {
        return json_decode((string) $body, true, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string, mixed> $result
     */
    private function createConnectionPoolMock(array $result): ConnectionPool&MockObject
    {
        $resultMock = $this->createMock(Result::class);
        $resultMock->method('fetchAssociative')->willReturn($result);

        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->method('select')->willReturnSelf();
        $queryBuilderMock->method('from')->willReturnSelf();
        $queryBuilderMock->method('where')->willReturnSelf();
        $queryBuilderMock->method('setMaxResults')->willReturnSelf();
        $queryBuilderMock->method('createNamedParameter')->willReturn(':dcValue1');
        $queryBuilderMock->method('expr')->willReturn($this->createMock(ExpressionBuilder::class));
        $queryBuilderMock->method('executeQuery')->willReturn($resultMock);

        $connectionPoolMock = $this->createMock(ConnectionPool::class);
        $connectionPoolMock->method('getQueryBuilderForTable')->willReturn($queryBuilderMock);

        return $connectionPoolMock;
    }
}
