/**
 * Theme Preview - live color preview injected into TCA form.
 *
 * Self-initializing: finds the color fields, injects preview HTML,
 * and updates on every color change.
 */

function hexToHsl(hex) {
    const clean = hex.replace('#', '');
    let r = parseInt(clean.slice(0, 2), 16) / 255;
    let g = parseInt(clean.slice(2, 4), 16) / 255;
    let b = parseInt(clean.slice(4, 6), 16) / 255;

    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h = 0, s = 0;
    const l = (max + min) / 2;

    if (max !== min) {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        if (max === r) h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
        else if (max === g) h = ((b - r) / d + 2) / 6;
        else h = ((r - g) / d + 4) / 6;
    }

    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
}

function deriveSidebarColor(hex, lightMode) {
    const { h } = hexToHsl(hex);
    return lightMode ? `hsl(${h}, 40%, 20%)` : `hsl(${h}, 20%, 10%)`;
}

function deriveIconAccent(hex, lightMode) {
    const { h } = hexToHsl(hex);
    return lightMode ? `hsl(${h}, 80%, 70%)` : `hsl(${h}, 60%, 60%)`;
}

function findInput(suffix) {
    return document.querySelector(
        `input[data-formengine-input-name$="[${suffix}]"], input[name$="[${suffix}]"]`
    );
}

function readColor(suffix) {
    const el = findInput(suffix);
    if (!el) return '';
    const val = el.value.trim();
    return /^#[0-9A-Fa-f]{6}$/.test(val) ? val : '';
}

function readAutoSecondary() {
    const el = document.querySelector('input[name$="[auto_secondary]"]');
    if (!el) return true;
    return el.checked || el.value === '1';
}

const MODULE_ICONS = `
<svg viewBox="0 0 16 16" width="14" height="14" fill="currentColor" opacity="0.7"><path d="M2 1h12a1 1 0 011 1v12a1 1 0 01-1 1H2a1 1 0 01-1-1V2a1 1 0 011-1zm1 2v10h10V3H3z"/></svg>
<svg viewBox="0 0 16 16" width="14" height="14" fill="currentColor" opacity="0.7"><path d="M1 2h14v2H1zm0 4h14v2H1zm0 4h14v2H1z"/></svg>
<svg viewBox="0 0 16 16" width="14" height="14" fill="currentColor" opacity="0.7"><path d="M2 2h5v5H2zm7 0h5v5H9zM2 9h5v5H2zm7 0h5v5H9z"/></svg>
<svg viewBox="0 0 16 16" width="14" height="14" fill="currentColor" opacity="0.7"><path d="M1 1h6v6H1zm8 0h6v6H9zM1 9h14v2H1zm0 4h10v2H1z"/></svg>
`.trim().split('\n');

function buildSidebar(mode) {
    const bg = mode === 'light' ? '#ccc' : '#222';
    const iconBg = mode === 'light' ? '#aaa' : '#333';
    const icons = MODULE_ICONS.map(svg =>
        '<div data-preview-icon style="width:22px;height:22px;border-radius:4px;background:' + iconBg + ';display:flex;align-items:center;justify-content:center;color:#fff;">' + svg + '</div>'
    ).join('');
    return '<div data-preview-sidebar style="width:44px;display:flex;flex-direction:column;align-items:center;padding:8px 0;gap:4px;background:' + bg + ';">' + icons + '</div>';
}

function buildPreviewPanel(mode) {
    const isLight = mode === 'light';
    const border = isLight ? '#ccc' : '#444';
    const contentBg = isLight ? '#f5f5f5' : '#1e1e1e';
    const headerBg = isLight ? '#ddd' : '#2d2d2d';
    const boxBg = isLight ? '#fff' : '#2a2a2a';
    const boxBorder = isLight ? '#e0e0e0' : '#444';
    return '<div data-preview-mode="' + mode + '" style="flex:1;">' +
        '<div style="margin-bottom:6px;font-weight:600;font-size:12px;">' + (isLight ? 'Light' : 'Dark') + '</div>' +
        '<div style="display:flex;border:1px solid ' + border + ';border-radius:4px;overflow:hidden;height:120px;">' +
            buildSidebar(mode) +
            '<div style="flex:1;display:flex;flex-direction:column;background:' + contentBg + ';">' +
                '<div data-preview-header style="height:28px;background:' + headerBg + ';display:flex;align-items:center;padding:0 8px;">' +
                    '<span style="color:#fff;font-size:10px;">Header</span>' +
                '</div>' +
                '<div style="flex:1;padding:6px;"><div style="height:100%;background:' + boxBg + ';border:1px solid ' + boxBorder + ';border-radius:2px;"></div></div>' +
            '</div>' +
        '</div>' +
    '</div>';
}

const PREVIEW_HTML = '<div data-theme-preview style="margin:16px 0;"><div style="display:flex;gap:16px;">' +
    buildPreviewPanel('light') + buildPreviewPanel('dark') +
'</div></div>';

function injectPreview() {
    if (document.querySelector('[data-theme-preview]')) return;

    const primaryField = findInput('primary_color');
    if (!primaryField) return;

    const formSection = primaryField.closest('.form-section, .tab-pane, fieldset');
    if (!formSection) return;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = PREVIEW_HTML;
    formSection.parentNode.insertBefore(wrapper.firstElementChild, formSection.nextSibling);
}

function updatePreview() {
    const container = document.querySelector('[data-theme-preview]');
    if (!container) return;

    const primaryColor = readColor('primary_color');
    if (!primaryColor) return;

    const secondaryColor = readColor('secondary_color');
    const autoSecondary = readAutoSecondary();
    const darkPrimary = readColor('darkmode_primary_color');
    const darkSecondary = readColor('darkmode_secondary_color');

    // Light mode
    const light = container.querySelector('[data-preview-mode="light"]');
    if (light) {
        const bg = (autoSecondary || !secondaryColor) ? deriveSidebarColor(primaryColor, true) : secondaryColor;
        light.querySelectorAll('[data-preview-sidebar]').forEach(el => el.style.backgroundColor = bg);
        light.querySelectorAll('[data-preview-header]').forEach(el => el.style.backgroundColor = bg);
        light.querySelectorAll('[data-preview-icon]').forEach(el => el.style.backgroundColor = deriveIconAccent(primaryColor, true));
    }

    // Dark mode
    const dark = container.querySelector('[data-preview-mode="dark"]');
    if (dark) {
        const ep = darkPrimary || primaryColor;
        const bg = darkSecondary || ((autoSecondary || !secondaryColor) ? deriveSidebarColor(ep, false) : secondaryColor);
        dark.querySelectorAll('[data-preview-sidebar]').forEach(el => el.style.backgroundColor = bg);
        dark.querySelectorAll('[data-preview-header]').forEach(el => el.style.backgroundColor = bg);
        dark.querySelectorAll('[data-preview-icon]').forEach(el => el.style.backgroundColor = deriveIconAccent(ep, false));
    }
}

let listenerController = new AbortController();

function attachListeners() {
    listenerController.abort();
    listenerController = new AbortController();
    const opts = { signal: listenerController.signal };

    ['primary_color', 'secondary_color', 'darkmode_primary_color', 'darkmode_secondary_color'].forEach(suffix => {
        const el = findInput(suffix);
        if (el) {
            el.addEventListener('input', updatePreview, opts);
            el.addEventListener('change', updatePreview, opts);
        }
    });

    const auto = document.querySelector('input[name$="[auto_secondary]"]');
    if (auto) auto.addEventListener('change', updatePreview, opts);
}

function init() {
    injectPreview();
    updatePreview();
    attachListeners();

    const tceForms = document.querySelector('.typo3-TCEforms');
    if (tceForms) {
        new MutationObserver(() => {
            requestAnimationFrame(() => {
                injectPreview();
                updatePreview();
                attachListeners();
            });
        }).observe(tceForms, { childList: true, subtree: true });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
