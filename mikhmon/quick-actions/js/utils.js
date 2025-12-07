/**
 * Utility Functions
 */

const Utils = {
  /**
   * Show loading spinner
   */
  showLoader() {
    document.getElementById('qa-loader').classList.add('qa-active');
  },

  /**
   * Hide loading spinner
   */
  hideLoader() {
    document.getElementById('qa-loader').classList.remove('qa-active');
  },

  /**
   * Show toast notification
   */
  showToast(message, type = 'success') {
    const container = document.getElementById('qa-toast-container');
    const toast = document.createElement('div');
    toast.className = `qa-toast qa-toast-${type}`;
    
    const icon = type === 'success' 
      ? '<polyline points="20 6 9 17 4 12"/>'
      : type === 'error'
      ? '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>'
      : '<line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>';
    
    toast.innerHTML = `
      <svg class="qa-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        ${icon}
      </svg>
      <div class="qa-toast-message">${message}</div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
      toast.style.animation = 'qa-toast-slide-in 0.3s ease reverse';
      setTimeout(() => toast.remove(), 300);
    }, APP_CONFIG.TOAST_DURATION);
  },

  /**
   * Debounce function
   */
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  /**
   * Escape HTML to prevent XSS
   */
  escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
  }
};

// Export utils
window.Utils = Utils;
