/**
 * Theme Preview - live color preview injected into TCA form.
 * Listens to TYPO3 FormEngine color picker changes and updates instantly.
 */

function hexToHsl(hex) {
    const clean = hex.replace('#', '');
    if (clean.length !== 6) return { h: 0, s: 0, l: 0 };
    const r = parseInt(clean.slice(0, 2), 16) / 255;
    const g = parseInt(clean.slice(2, 4), 16) / 255;
    const b = parseInt(clean.slice(4, 6), 16) / 255;
    const max = Math.max(r, g, b), min = Math.min(r, g, b);
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

function deriveSidebarColor(hex, light) {
    const { h } = hexToHsl(hex);
    return light ? `hsl(${h}, 40%, 20%)` : `hsl(${h}, 20%, 10%)`;
}

function deriveIconAccent(hex, light) {
    const { h } = hexToHsl(hex);
    return light ? `hsl(${h}, 80%, 70%)` : `hsl(${h}, 60%, 60%)`;
}

/**
 * Read a color field value. TYPO3 FormEngine stores the real value in
 * a hidden input whose name ends with [fieldname]. The visible input
 * is a formengine-input wrapper. We try both, plus native color pickers.
 */
function readColor(suffix) {
    // Hidden real value input (TYPO3 FormEngine pattern)
    const hidden = document.querySelector(
        `input[name$="[${suffix}]"][type="hidden"], ` +
        `input[name$="[${suffix}]"]:not([data-formengine-input-name])`
    );
    if (hidden && /^#[0-9A-Fa-f]{6}$/.test(hidden.value)) return hidden.value;

    // Visible formengine input
    const visible = document.querySelector(`input[data-formengine-input-name$="[${suffix}]"]`);
    if (visible && /^#[0-9A-Fa-f]{6}$/.test(visible.value)) return visible.value;

    // Native color input
    const color = document.querySelector(`input[type="color"][name$="[${suffix}]"]`);
    if (color) return color.value;

    return '';
}

function readAutoSecondary() {
    // TYPO3 checkbox: hidden input with value 0/1
    const hidden = document.querySelector('input[name$="[auto_secondary]"][type="hidden"]');
    if (hidden) return hidden.value === '1';
    const checkbox = document.querySelector('input[name$="[auto_secondary]"]');
    if (checkbox) return checkbox.checked || checkbox.value === '1';
    return true;
}

// SVG module icons for sidebar
const ICONS = [
    '<svg viewBox="0 0 16 16" width="14" height="14"><path d="M3 1h10a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V3a2 2 0 012-2zm0 2v10h10V3H3zm2 2h6v2H5V5zm0 4h4v2H5V9z" fill="currentColor"/></svg>',
    '<svg viewBox="0 0 16 16" width="14" height="14"><path d="M1 3h14v2H1V3zm0 4h14v2H1V7zm0 4h10v2H1v-2z" fill="currentColor"/></svg>',
    '<svg viewBox="0 0 16 16" width="14" height="14"><path d="M1 1h6v6H1V1zm8 0h6v6H9V1zM1 9h6v6H1V9zm8 0h6v6H9V9z" fill="currentColor"/></svg>',
    '<svg viewBox="0 0 16 16" width="14" height="14"><path d="M2 2h5v5H2V2zm7 0h5v3H9V2zM2 9h5v5H2V9zm7-1h5v6H9V8z" fill="currentColor"/></svg>',
    '<svg viewBox="0 0 16 16" width="14" height="14"><path d="M8 1l2.5 5H14l-4 3.5L11.5 15 8 11.5 4.5 15 6 9.5 2 6h3.5z" fill="currentColor"/></svg>',
];

function buildPanel(mode) {
    const L = mode === 'light';
    const iconHtml = ICONS.map(svg =>
        `<div data-preview-icon style="width:24px;height:24px;border-radius:4px;display:flex;align-items:center;justify-content:center;color:${L ? '#aaa' : '#555'};background:transparent;">${svg}</div>`
    ).join('');

    return `<div data-preview-mode="${mode}" style="flex:1;">
        <div style="margin-bottom:6px;font-weight:600;font-size:12px;">${L ? 'Light' : 'Dark'}</div>
        <div style="display:flex;border:1px solid ${L ? '#ccc' : '#444'};border-radius:6px;overflow:hidden;height:140px;">
            <div data-preview-sidebar style="width:48px;display:flex;flex-direction:column;align-items:center;padding:10px 0;gap:4px;background:${L ? '#ccc' : '#222'};">
                ${iconHtml}
            </div>
            <div style="flex:1;display:flex;flex-direction:column;background:${L ? '#f5f5f5' : '#1e1e1e'};">
                <div data-preview-header style="height:32px;background:${L ? '#ddd' : '#2d2d2d'};display:flex;align-items:center;padding:0 10px;">
                    <span style="color:#fff;font-size:11px;font-weight:600;">Header</span>
                </div>
                <div style="flex:1;padding:8px;">
                    <div style="height:100%;background:${L ? '#fff' : '#2a2a2a'};border:1px solid ${L ? '#e0e0e0' : '#444'};border-radius:3px;"></div>
                </div>
            </div>
        </div>
    </div>`;
}

const PREVIEW_HTML = `<div data-theme-preview style="margin:16px 0;padding:16px;background:var(--typo3-surface-container-lowest, #fafafa);border:1px solid var(--typo3-surface-container-high, #e0e0e0);border-radius:8px;">
    <div style="margin-bottom:10px;font-weight:700;font-size:13px;">Theme Preview</div>
    <div style="display:flex;gap:16px;">${buildPanel('light')}${buildPanel('dark')}</div>
</div>`;

function injectPreview() {
    if (document.querySelector('[data-theme-preview]')) return;

    // Find the colors palette or primary_color field
    const allFormSections = document.querySelectorAll('.form-section');
    let target = null;
    for (const section of allFormSections) {
        if (section.querySelector('input[name$="[primary_color]"], input[data-formengine-input-name$="[primary_color]"]')) {
            target = section;
            break;
        }
    }
    if (!target) return;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = PREVIEW_HTML;
    target.parentNode.insertBefore(wrapper.firstElementChild, target.nextSibling);
}

function updatePreview() {
    const container = document.querySelector('[data-theme-preview]');
    if (!container) return;

    const primary = readColor('primary_color');
    if (!primary) return;

    const secondary = readColor('secondary_color');
    const autoSec = readAutoSecondary();
    const dkPrimary = readColor('darkmode_primary_color');
    const dkSecondary = readColor('darkmode_secondary_color');

    // Light
    const light = container.querySelector('[data-preview-mode="light"]');
    if (light) {
        const bg = (autoSec || !secondary) ? deriveSidebarColor(primary, true) : secondary;
        const iconColor = deriveIconAccent(primary, true);
        light.querySelectorAll('[data-preview-sidebar]').forEach(el => el.style.backgroundColor = bg);
        light.querySelectorAll('[data-preview-header]').forEach(el => el.style.backgroundColor = bg);
        light.querySelectorAll('[data-preview-icon]').forEach(el => el.style.color = iconColor);
    }

    // Dark
    const dark = container.querySelector('[data-preview-mode="dark"]');
    if (dark) {
        const ep = dkPrimary || primary;
        const bg = dkSecondary || ((autoSec || !secondary) ? deriveSidebarColor(ep, false) : secondary);
        const iconColor = deriveIconAccent(ep, false);
        dark.querySelectorAll('[data-preview-sidebar]').forEach(el => el.style.backgroundColor = bg);
        dark.querySelectorAll('[data-preview-header]').forEach(el => el.style.backgroundColor = bg);
        dark.querySelectorAll('[data-preview-icon]').forEach(el => el.style.color = iconColor);
    }
}

let ctrl = new AbortController();

function attachListeners() {
    ctrl.abort();
    ctrl = new AbortController();
    const opts = { signal: ctrl.signal };

    // Listen on ALL inputs inside the form - covers hidden, visible, and color type inputs.
    // TYPO3 FormEngine updates hidden inputs on change, so we listen broadly.
    const form = document.querySelector('.typo3-TCEforms');
    if (form) {
        form.addEventListener('input', updatePreview, opts);
        form.addEventListener('change', updatePreview, opts);
    }
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
        }).observe(tceForms, { childList: true, subtree: true, attributes: true });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
