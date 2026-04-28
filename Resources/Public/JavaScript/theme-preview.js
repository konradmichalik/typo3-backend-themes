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
    const { h, s } = hexToHsl(hex);
    return light ? `hsl(${h}, ${s}%, 75%)` : `hsl(${h}, ${s}%, 70%)`;
}

function isDarkColor(hex) {
    const r = parseInt(hex.slice(1, 3), 16) / 255;
    const g = parseInt(hex.slice(3, 5), 16) / 255;
    const b = parseInt(hex.slice(5, 7), 16) / 255;
    return (0.2126 * r + 0.7152 * g + 0.0722 * b) < 0.5;
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

// Original TYPO3 module icons with multi-color support:
// - currentColor = main icon color (white on dark sidebar)
// - var(--icon-color-accent) = accent color derived from primary
const ICONS = [
    // module-page
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="currentColor" d="M34.34 16 44 25.66V48H20V16h14.34M36 12H18c-1.1 0-2 .9-2 2v36c0 1.1.9 2 2 2h28c1.1 0 2-.9 2-2V24L36 12Z"/><path fill="var(--icon-color-accent, #ff8700)" d="M38 34H26c-1.1 0-2-.9-2-2s.9-2 2-2h12c1.1 0 2 .9 2 2s-.9 2-2 2ZM38 42H26c-1.1 0-2-.9-2-2s.9-2 2-2h12c1.1 0 2 .9 2 2s-.9 2-2 2Z"/><g opacity=".4"><path fill="currentColor" d="M34 16v7.67c0 1.12 1.19 2.33 2.33 2.33H44v-.34L34.34 16H34Z"/></g></svg>',
    // module-list
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="var(--icon-color-accent, #ff8700)" d="M48 16v28H16V16h32m2-4H14c-1.1 0-2 .9-2 2v32c0 1.1.9 2 2 2h36c1.1 0 2-.9 2-2V14c0-1.1-.9-2-2-2Z"/><path fill="currentColor" d="M22 20h2v4h-2c-1.1 0-2-.9-2-2s.9-2 2-2ZM22 28h2v4h-2c-1.1 0-2-.9-2-2s.9-2 2-2ZM22 36h2v4h-2c-1.1 0-2-.9-2-2s.9-2 2-2Z"/><g fill="currentColor" opacity=".4"><path d="M42 24H24v-4h18c1.1 0 2 .9 2 2s-.9 2-2 2ZM42 32H24v-4h18c1.1 0 2 .9 2 2s-.9 2-2 2ZM42 40H24v-4h18c1.1 0 2 .9 2 2s-.9 2-2 2Z"/></g></svg>',
    // module-file
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><circle cx="32" cy="32" r="4" fill="currentColor"/><path fill="currentColor" d="M42 20H14c-1.1 0-2 .9-2 2v28c0 1.1.9 2 2 2h28c1.1 0 2-.9 2-2V22c0-1.1-.9-2-2-2Zm-2 4v20.05l-4-3.25-6.4 3.2-8-6.4-5.6 4.48V24h24Z"/><path fill="var(--icon-color-accent, #ff8700)" d="M50 12H22c-1.1 0-2 .9-2 2v6h22c1.1 0 2 .9 2 2v22h6c1.1 0 2-.9 2-2V14c0-1.1-.9-2-2-2Z"/></svg>',
    // module-dashboard
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="currentColor" d="M50 42h-4v-4c0-1.1-.9-2-2-2s-2 .9-2 2v4h-4c-1.1 0-2 .9-2 2s.9 2 2 2h4v4c0 1.1.9 2 2 2s2-.9 2-2v-4h4c1.1 0 2-.9 2-2s-.9-2-2-2Z"/><path fill="var(--icon-color-accent, #ff8700)" d="M26 16v10H16V16h10m2-4H14c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V14c0-1.1-.9-2-2-2Z"/><g fill="currentColor" opacity=".4"><path d="M50 30H36c-1.1 0-2-.9-2-2V14c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2ZM28 52H14c-1.1 0-2-.9-2-2V36c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2Z"/></g></svg>',
    // module-web
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><g opacity=".4"><path fill="currentColor" d="M48 28.6H37.79c-1 0-2.04-1.09-2.04-2.1V16L48 28.6Z"/></g><path fill="currentColor" d="M35.81 20 44 28.42V48H24V20h11.81m1.69-4H21.75c-.97 0-1.75.81-1.75 1.8v32.4c0 .99.78 1.8 1.75 1.8h24.5c.97 0 1.75-.81 1.75-1.8V26.8L37.5 16Z"/><path fill="var(--icon-color-accent, #ff8700)" d="M20 17.8c0-.99.78-1.8 1.75-1.8H37.5l2.5 2.57V14c0-1.1-.9-2-2-2H14c-1.1 0-2 .9-2 2v32c0 1.1.9 2 2 2h6V17.8Z"/></svg>',
];

function buildPanel(mode) {
    const L = mode === 'light';
    const iconHtml = ICONS.map(svg =>
        `<div data-preview-icon style="width:24px;height:24px;border-radius:4px;display:flex;align-items:center;justify-content:center;">${svg}</div>`
    ).join('');

    return `<div data-preview-mode="${mode}" style="flex:1;">
        <div style="margin-bottom:6px;font-weight:600;font-size:12px;">${L ? 'Light' : 'Dark'}</div>
        <div style="border:1px solid ${L ? '#ccc' : '#444'};border-radius:6px;overflow:hidden;height:140px;display:flex;flex-direction:column;">
            <div data-preview-header style="height:32px;min-height:32px;background:${L ? '#ddd' : '#2d2d2d'};display:flex;align-items:center;padding:0 10px;">
                <span style="color:#fff;font-size:11px;font-weight:600;">Header</span>
            </div>
            <div style="flex:1;display:flex;overflow:hidden;">
                <div data-preview-sidebar style="width:48px;min-width:48px;display:flex;flex-direction:column;align-items:center;padding:8px 0;gap:2px;background:${L ? '#ccc' : '#222'};color:${L ? 'rgba(255,255,255,0.9)' : 'rgba(255,255,255,0.7)'};">
                    ${iconHtml}
                </div>
                <div style="flex:1;padding:8px;background:${L ? '#f5f5f5' : '#1e1e1e'};">
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

    const headerColor = readColor('header_color');
    const sidebarColor = readColor('sidebar_color');
    const dkPrimary = readColor('darkmode_primary_color');
    const dkHeader = readColor('darkmode_header_color');
    const dkSidebar = readColor('darkmode_sidebar_color');
    const derived = deriveSidebarColor(primary, true);

    // Light: sidebar_color is base, header_color inherits from it
    const light = container.querySelector('[data-preview-mode="light"]');
    if (light) {
        const sBg = sidebarColor || derived;
        const hBg = headerColor || sBg;
        const sText = isDarkColor(sBg) ? '#fff' : '#1e1e1e';
        const hText = isDarkColor(hBg) ? '#fff' : '#1e1e1e';
        light.querySelectorAll('[data-preview-sidebar]').forEach(el => {
            el.style.backgroundColor = sBg;
            el.style.color = sText;
            el.style.setProperty('--icon-color-accent', primary);
        });
        light.querySelectorAll('[data-preview-header]').forEach(el => {
            el.style.backgroundColor = hBg;
            const span = el.querySelector('span');
            if (span) span.style.color = hText;
        });
    }

    // Dark: dk overrides > derive from dk primary
    const dark = container.querySelector('[data-preview-mode="dark"]');
    if (dark) {
        const ep = dkPrimary || primary;
        const derivedDk = deriveSidebarColor(ep, false);
        const sBg = dkSidebar || derivedDk;
        const hBg = dkHeader || sBg;
        const sText = isDarkColor(sBg) ? '#fff' : '#1e1e1e';
        const hText = isDarkColor(hBg) ? '#fff' : '#1e1e1e';
        dark.querySelectorAll('[data-preview-sidebar]').forEach(el => {
            el.style.backgroundColor = sBg;
            el.style.color = sText;
            el.style.setProperty('--icon-color-accent', ep);
        });
        dark.querySelectorAll('[data-preview-header]').forEach(el => {
            el.style.backgroundColor = hBg;
            const span = el.querySelector('span');
            if (span) span.style.color = hText;
        });
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
