/**
 * Authentication Module
 */

const Auth = {
  /**
   * Login user
   */
  async login(username, password) {
    Utils.showLoader();
    try {
      const response = await API.login(username, password);
      
      if (response.success) {
        APP_STATE.isAuthenticated = true;
        APP_STATE.token = response.data.token;
        APP_STATE.username = response.data.username;
        
        localStorage.setItem(APP_CONFIG.STORAGE_KEYS.TOKEN, response.data.token);
        localStorage.setItem(APP_CONFIG.STORAGE_KEYS.USERNAME, response.data.username);
        
        UI.showDashboard();
        Router.loadRouters();
        Utils.showToast('Login successful!');
      }
    } catch (error) {
      Utils.showToast(error.message || 'Login failed', 'error');
    } finally {
      Utils.hideLoader();
    }
  },

  /**
   * Logout user
   */
  logout() {
    APP_STATE.isAuthenticated = false;
    APP_STATE.token = null;
    APP_STATE.username = null;
    APP_STATE.routers = [];
    
    localStorage.removeItem(APP_CONFIG.STORAGE_KEYS.TOKEN);
    localStorage.removeItem(APP_CONFIG.STORAGE_KEYS.USERNAME);
    
    UI.showLogin();
    Utils.showToast('Logged out successfully');
  },

  /**
   * Check if user is authenticated
   */
  checkAuth() {
    const token = localStorage.getItem(APP_CONFIG.STORAGE_KEYS.TOKEN);
    const username = localStorage.getItem(APP_CONFIG.STORAGE_KEYS.USERNAME);
    
    if (token && username) {
      APP_STATE.token = token;
      APP_STATE.username = username;
      APP_STATE.isAuthenticated = true;
      UI.showDashboard();
      Router.loadRouters();
    } else {
      UI.showLogin();
    }
  }
};

// Export Auth
window.Auth = Auth;
