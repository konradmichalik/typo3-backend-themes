/**
 * Theme Live - re-styles the backend shell after a theme change without a
 * full page reload.
 *
 * The User Settings (setup) module is rendered inside the content iframe. When
 * a user saves a changed theme, only that iframe reloads - the surrounding
 * backend shell (top bar, module menu, sidebar) keeps its old CSS. This module
 * runs on the reloaded settings page, fetches the freshly generated CSS for the
 * now-active theme and writes it into the shell document (window.top), so the
 * chrome updates instantly.
 */

import AjaxRequest from '@typo3/core/ajax/ajax-request.js';

const STYLE_ID = 'typo3-backend-themes-live';

function isUserSettingsPage() {
    return document.querySelector('[name*="user_settings__theme"]') !== null;
}

function applyCssToShell(doc, css) {
    let style = doc.getElementById(STYLE_ID);
    if (style === null) {
        style = doc.createElement('style');
        style.id = STYLE_ID;
        // Match the page's CSP nonce so the injected style is allowed under a
        // strict policy; ignored when style-src permits 'unsafe-inline'.
        const noncedEl = doc.querySelector('style[nonce], script[nonce]');
        if (noncedEl && noncedEl.nonce) {
            style.nonce = noncedEl.nonce;
        }
        doc.head.appendChild(style);
    }
    style.textContent = css;
}

async function syncShell() {
    const shell = window.top || window;
    const url = shell.TYPO3?.settings?.ajaxUrls?.['backend_themes_css'];
    if (!url) {
        return;
    }

    try {
        const response = await new AjaxRequest(url).get();
        const data = await response.resolve();
        applyCssToShell(shell.document, data.css ?? '');
    } catch {
        // Network/route errors are non-critical: a full reload still applies
        // the correct theme, so we fail silently.
    }
}

if (isUserSettingsPage()) {
    syncShell();
}
