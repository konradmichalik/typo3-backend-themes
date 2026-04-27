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

const PREVIEW_HTML = `
<div data-theme-preview style="margin:16px 0;">
    <div style="display:flex;gap:16px;">
        <div data-preview-mode="light" style="flex:1;">
            <div style="margin-bottom:6px;font-weight:600;font-size:12px;">Light</div>
            <div style="display:flex;border:1px solid #ccc;border-radius:4px;overflow:hidden;height:100px;">
                <div data-preview-sidebar style="width:40px;display:flex;flex-direction:column;align-items:center;padding:8px 0;gap:6px;background:#ccc;">
                    <div data-preview-icon style="width:16px;height:16px;border-radius:3px;background:#aaa;"></div>
                    <div data-preview-icon style="width:16px;height:16px;border-radius:3px;background:#aaa;"></div>
                    <div data-preview-icon style="width:16px;height:16px;border-radius:3px;background:#aaa;"></div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;background:#f5f5f5;">
                    <div data-preview-header style="height:28px;background:#ddd;display:flex;align-items:center;padding:0 8px;">
                        <span style="color:#fff;font-size:10px;">Header</span>
                    </div>
                    <div style="flex:1;padding:6px;"><div style="height:100%;background:#fff;border:1px solid #e0e0e0;border-radius:2px;"></div></div>
                </div>
            </div>
        </div>
        <div data-preview-mode="dark" style="flex:1;">
            <div style="margin-bottom:6px;font-weight:600;font-size:12px;">Dark</div>
            <div style="display:flex;border:1px solid #444;border-radius:4px;overflow:hidden;height:100px;">
                <div data-preview-sidebar style="width:40px;display:flex;flex-direction:column;align-items:center;padding:8px 0;gap:6px;background:#222;">
                    <div data-preview-icon style="width:16px;height:16px;border-radius:3px;background:#333;"></div>
                    <div data-preview-icon style="width:16px;height:16px;border-radius:3px;background:#333;"></div>
                    <div data-preview-icon style="width:16px;height:16px;border-radius:3px;background:#333;"></div>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;background:#1e1e1e;">
                    <div data-preview-header style="height:28px;background:#2d2d2d;display:flex;align-items:center;padding:0 8px;">
                        <span style="color:#fff;font-size:10px;">Header</span>
                    </div>
                    <div style="flex:1;padding:6px;"><div style="height:100%;background:#2a2a2a;border:1px solid #444;border-radius:2px;"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>`;

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
