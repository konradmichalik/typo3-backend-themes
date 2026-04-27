/**
 * Shows a reload notification when the theme selection changes in User Settings.
 * Listens for changes on the theme select and displays a TYPO3 notification.
 */
import Notification from '@typo3/backend/notification.js';

function init() {
    const form = document.querySelector('form[name="usersetup"]');
    if (!form) return;

    const themeSelect = form.querySelector('select[name*="[theme]"]');
    if (!themeSelect) return;

    const initialValue = themeSelect.value;

    themeSelect.addEventListener('change', () => {
        if (themeSelect.value !== initialValue) {
            Notification.info(
                'Theme changed',
                'Please save and reload the page for the theme to take effect.',
                5
            );
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
