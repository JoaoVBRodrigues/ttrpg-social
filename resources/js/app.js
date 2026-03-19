import './bootstrap';

const storageKey = 'ttrpg-theme';

const applyTheme = (theme) => {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.dataset.theme = theme;
};

const preferredTheme = () => {
    const stored = window.localStorage.getItem(storageKey);

    if (stored === 'light' || stored === 'dark') {
        return stored;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
};

const refreshThemeControls = (theme) => {
    document.querySelectorAll('[data-theme-label]').forEach((element) => {
        element.textContent = theme === 'dark' ? 'Dark' : 'Light';
    });
};

const initializeThemeControls = () => {
    const theme = preferredTheme();

    applyTheme(theme);
    refreshThemeControls(theme);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';

            window.localStorage.setItem(storageKey, nextTheme);
            applyTheme(nextTheme);
            refreshThemeControls(nextTheme);
        });
    });
};

document.addEventListener('DOMContentLoaded', initializeThemeControls);
