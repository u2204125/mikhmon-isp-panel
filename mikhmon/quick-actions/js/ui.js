/**
 * UI Module
 * Handles UI state and transitions
 */

const UI = {
  /**
   * Show login screen
   */
  showLogin() {
    document.getElementById('qa-login').classList.remove('qa-hidden');
    document.getElementById('qa-dashboard').classList.remove('qa-active');
    document.getElementById('qa-header').style.display = 'none';
  },

  /**
   * Show dashboard
   */
  showDashboard() {
    document.getElementById('qa-login').classList.add('qa-hidden');
    document.getElementById('qa-dashboard').classList.add('qa-active');
    document.getElementById('qa-header').style.display = 'flex';
    document.getElementById('qa-user-display').textContent = `${APP_STATE.username}@system`;
  }
};

// Export UI
window.UI = UI;
