/**
 * Theme Module
 */

const Theme = {
  /**
   * Toggle theme
   */
  toggle() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem(APP_CONFIG.STORAGE_KEYS.THEME, newTheme);
    APP_STATE.theme = newTheme;
  },

  /**
   * Load saved theme
   */
  load() {
    const savedTheme = localStorage.getItem(APP_CONFIG.STORAGE_KEYS.THEME) || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    APP_STATE.theme = savedTheme;
  }
};

// Export Theme
window.Theme = Theme;
