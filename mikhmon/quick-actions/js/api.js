/**
 * API Service
 * Handles all API requests
 */

const API = {
  /**
   * Generic API request handler
   */
  async request(endpoint, options = {}) {
    const config = {
      headers: {
        'Content-Type': 'application/json',
        ...(APP_STATE.token && { 'Authorization': `Bearer ${APP_STATE.token}` })
      },
      ...options
    };

    try {
      const response = await fetch(`${APP_CONFIG.API_BASE}/${endpoint}`, config);
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || 'Request failed');
      }
      
      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  },

  /**
   * Login user
   */
  async login(username, password) {
    return this.request('login.php', {
      method: 'POST',
      body: JSON.stringify({ username, password })
    });
  },

  /**
   * Get all routers
   */
  async getRouters() {
    return this.request('routers.php');
  },

  /**
   * Add new router
   */
  async addRouter(routerData) {
    return this.request('routers.php', {
      method: 'POST',
      body: JSON.stringify(routerData)
    });
  }
};

// Export API
window.API = API;
