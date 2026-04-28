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

namespace KonradMichalik\Typo3BackendThemes\Backend\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

/**
 * ThemePreviewElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ThemePreviewElement extends AbstractNode
{
    /** @return array<string, mixed> */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@konradmichalik/backend-themes/theme-preview.js',
        );

        return $resultArray;
    }
}
