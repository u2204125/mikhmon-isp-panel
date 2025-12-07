/**
 * Application Configuration
 * Loads settings from environment or config file
 */

const APP_CONFIG = {
  API_BASE: './quick-actions/api',
  STORAGE_KEYS: {
    TOKEN: 'qa_auth_token',
    USERNAME: 'qa_username',
    THEME: 'qa_theme'
  },
  TOKEN_EXPIRY: 30 * 24 * 60 * 60, // 30 days in seconds
  TOAST_DURATION: 3000,
  SEARCH_DEBOUNCE: 300
};

// Export config
window.APP_CONFIG = APP_CONFIG;
