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

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;

final class ThemePreviewElement extends AbstractFormElement
{
    /** @return array<string, mixed> */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['html'] = $this->buildPreviewHtml();
        $resultArray['javaScriptModules'][] = \TYPO3\CMS\Core\Page\JavaScriptModuleInstruction::create(
            '@konradmichalik/backend-themes/theme-preview.js',
        );
        return $resultArray;
    }

    private function buildPreviewHtml(): string
    {
        return <<<HTML
<div data-theme-preview>
    <div style="display:flex;gap:16px;margin:16px 0;">
        <div data-preview-mode="light" style="flex:1;">
            <div style="margin-bottom:6px;"><strong>Light Mode</strong></div>
            <div style="display:flex;border:1px solid #ccc;border-radius:4px;overflow:hidden;height:120px;">
                <div data-preview-sidebar style="width:48px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:8px 0;gap:8px;background:#cccccc;">
                    <div data-preview-icon style="width:20px;height:20px;border-radius:4px;background:#aaaaaa;"></div>
                    <div data-preview-icon style="width:20px;height:20px;border-radius:4px;background:#aaaaaa;"></div>
                    <div data-preview-icon style="width:20px;height:20px;border-radius:4px;background:#aaaaaa;"></div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;background:#f5f5f5;">
                    <div data-preview-header style="height:32px;background:#dddddd;display:flex;align-items:center;padding:0 8px;">
                        <span style="color:#fff;font-size:11px;font-weight:600;">Header</span>
                    </div>
                    <div style="flex:1;padding:8px;">
                        <div style="height:100%;background:#fff;border:1px solid #e0e0e0;border-radius:3px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div data-preview-mode="dark" style="flex:1;">
            <div style="margin-bottom:6px;"><strong>Dark Mode</strong></div>
            <div style="display:flex;border:1px solid #444;border-radius:4px;overflow:hidden;height:120px;">
                <div data-preview-sidebar style="width:48px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:8px 0;gap:8px;background:#222222;">
                    <div data-preview-icon style="width:20px;height:20px;border-radius:4px;background:#333333;"></div>
                    <div data-preview-icon style="width:20px;height:20px;border-radius:4px;background:#333333;"></div>
                    <div data-preview-icon style="width:20px;height:20px;border-radius:4px;background:#333333;"></div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;background:#1e1e1e;">
                    <div data-preview-header style="height:32px;background:#2d2d2d;display:flex;align-items:center;padding:0 8px;">
                        <span style="color:#fff;font-size:11px;font-weight:600;">Header</span>
                    </div>
                    <div style="flex:1;padding:8px;">
                        <div style="height:100%;background:#2a2a2a;border:1px solid #444;border-radius:3px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
    }
}
