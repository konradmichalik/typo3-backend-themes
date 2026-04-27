/**
 * Theme Preview - live color preview for ThemePreviewElement
 *
 * Plain ES module, no build step required.
 */

/**
 * Converts a hex color string to an HSL object.
 *
 * @param {string} hex
 * @returns {{ h: number, s: number, l: number }}
 */
function hexToHsl(hex) {
    let r = 0, g = 0, b = 0;
    const clean = hex.replace('#', '');
    if (clean.length === 3) {
        r = parseInt(clean[0] + clean[0], 16);
        g = parseInt(clean[1] + clean[1], 16);
        b = parseInt(clean[2] + clean[2], 16);
    } else if (clean.length === 6) {
        r = parseInt(clean.slice(0, 2), 16);
        g = parseInt(clean.slice(2, 4), 16);
        b = parseInt(clean.slice(4, 6), 16);
    }

    r /= 255;
    g /= 255;
    b /= 255;

    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h = 0, s = 0;
    const l = (max + min) / 2;

    if (max !== min) {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
            case g: h = ((b - r) / d + 2) / 6; break;
            case b: h = ((r - g) / d + 4) / 6; break;
        }
    }

    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
}

/**
 * Derives a sidebar background color from a primary hex color.
 *
 * @param {string} hex
 * @param {boolean} lightMode
 * @returns {string} HSL CSS string
 */
function deriveSidebarColor(hex, lightMode) {
    const { h } = hexToHsl(hex);
    return lightMode
        ? `hsl(${h}, 40%, 20%)`
        : `hsl(${h}, 20%, 10%)`;
}

/**
 * Derives an icon accent color from a primary hex color.
 *
 * @param {string} hex
 * @param {boolean} lightMode
 * @returns {string} HSL CSS string
 */
function deriveIconAccent(hex, lightMode) {
    const { h } = hexToHsl(hex);
    return lightMode
        ? `hsl(${h}, 80%, 70%)`
        : `hsl(${h}, 60%, 60%)`;
}

/**
 * Finds the closest FormEngine field by name suffix within the TCEforms scope.
 *
 * @param {string} suffix
 * @returns {HTMLInputElement|null}
 */
function findInput(suffix) {
    return document.querySelector(`[name$="[${suffix}]"], [name$="_${suffix}"], [data-field-name="${suffix}"]`);
}

/**
 * Reads the current value of a color input by name suffix.
 *
 * @param {string} suffix
 * @returns {string} hex color or empty string
 */
function readColor(suffix) {
    const el = findInput(suffix);
    if (!el) return '';
    const val = el.value.trim();
    return val.startsWith('#') ? val : (val ? '#' + val : '');
}

/**
 * Reads whether the auto_secondary checkbox is checked.
 *
 * @returns {boolean}
 */
function readAutoSecondary() {
    const el = findInput('auto_secondary');
    if (!el) return true;
    return el.checked;
}

/**
 * Updates the live preview containers based on current color input values.
 */
function updatePreview() {
    const container = document.querySelector('[data-theme-preview]');
    if (!container) return;

    const primaryColor = readColor('primary_color');
    const secondaryColor = readColor('secondary_color');
    const autoSecondary = readAutoSecondary();
    const darkmodePrimaryColor = readColor('darkmode_primary_color');
    const darkmodeSecondaryColor = readColor('darkmode_secondary_color');

    if (!primaryColor) return;

    // Light mode
    const lightContainer = container.querySelector('[data-preview-mode="light"]');
    if (lightContainer) {
        const sidebarBg = (autoSecondary || !secondaryColor)
            ? deriveSidebarColor(primaryColor, true)
            : secondaryColor;
        const headerBg = primaryColor;
        const iconColor = deriveIconAccent(primaryColor, true);

        const sidebar = lightContainer.querySelector('[data-preview-sidebar]');
        if (sidebar) sidebar.style.backgroundColor = sidebarBg;

        const header = lightContainer.querySelector('[data-preview-header]');
        if (header) {
            header.style.backgroundColor = headerBg;
            const span = header.querySelector('span');
            if (span) span.style.color = '#fff';
        }

        lightContainer.querySelectorAll('[data-preview-icon]').forEach(icon => {
            icon.style.backgroundColor = iconColor;
        });
    }

    // Dark mode
    const darkContainer = container.querySelector('[data-preview-mode="dark"]');
    if (darkContainer) {
        const effectivePrimary = darkmodePrimaryColor || primaryColor;
        const effectiveSecondary = darkmodeSecondaryColor || '';

        const sidebarBg = (autoSecondary || !effectiveSecondary)
            ? deriveSidebarColor(effectivePrimary, false)
            : effectiveSecondary;
        const headerBg = effectivePrimary;
        const iconColor = deriveIconAccent(effectivePrimary, false);

        const sidebar = darkContainer.querySelector('[data-preview-sidebar]');
        if (sidebar) sidebar.style.backgroundColor = sidebarBg;

        const header = darkContainer.querySelector('[data-preview-header]');
        if (header) {
            header.style.backgroundColor = headerBg;
            const span = header.querySelector('span');
            if (span) span.style.color = '#fff';
        }

        darkContainer.querySelectorAll('[data-preview-icon]').forEach(icon => {
            icon.style.backgroundColor = iconColor;
        });
    }
}

/**
 * AbortController to prevent listener accumulation on FormEngine reloads.
 */
let listenerController = new AbortController();

/**
 * Attaches change/input listeners to all relevant color inputs and the
 * auto_secondary checkbox. Previous listeners are cleaned up via AbortController.
 */
function attachListeners() {
    listenerController.abort();
    listenerController = new AbortController();
    const opts = { signal: listenerController.signal };

    const fields = [
        'primary_color',
        'secondary_color',
        'darkmode_primary_color',
        'darkmode_secondary_color',
    ];

    fields.forEach(suffix => {
        const el = findInput(suffix);
        if (el) {
            el.addEventListener('input', updatePreview, opts);
            el.addEventListener('change', updatePreview, opts);
        }
    });

    const autoSecondaryEl = findInput('auto_secondary');
    if (autoSecondaryEl) {
        autoSecondaryEl.addEventListener('change', updatePreview, opts);
    }
}

/**
 * Initializes the preview on DOMContentLoaded and observes FormEngine
 * dynamic reloads via MutationObserver.
 */
function init() {
    updatePreview();
    attachListeners();

    const tceForms = document.querySelector('.typo3-TCEforms');
    if (tceForms) {
        const observer = new MutationObserver(() => {
            requestAnimationFrame(() => {
                updatePreview();
                attachListeners();
            });
        });
        observer.observe(tceForms, { childList: true, subtree: true });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
