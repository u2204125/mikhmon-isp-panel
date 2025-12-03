/**
 * Application State Management
 */

const APP_STATE = {
  isAuthenticated: false,
  token: null,
  username: null,
  routers: [],
  filteredRouters: [],
  theme: 'dark'
};

// Export state
window.APP_STATE = APP_STATE;
