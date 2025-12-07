/**
 * Application Entry Point
 * Initializes the application and sets up event listeners
 */

document.addEventListener('DOMContentLoaded', () => {
  // Load theme
  Theme.load();
  
  // Check authentication
  Auth.checkAuth();
  
  // Login form
  document.getElementById('qa-login-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const username = document.getElementById('login-username').value;
    const password = document.getElementById('login-password').value;
    Auth.login(username, password);
  });
  
  // Logout button
  document.getElementById('qa-logout-btn').addEventListener('click', () => {
    Auth.logout();
  });
  
  // Theme toggle
  document.getElementById('qa-theme-toggle').addEventListener('click', () => {
    Theme.toggle();
  });
  
  // Add router buttons
  document.querySelectorAll('.qa-add-router-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      Modal.open();
    });
  });
  
  // Modal close buttons
  document.querySelector('.qa-modal-close').addEventListener('click', () => {
    Modal.close();
  });
  
  document.querySelector('.qa-modal-cancel').addEventListener('click', () => {
    Modal.close();
  });
  
  // Close modal on backdrop click
  document.getElementById('qa-add-router-modal').addEventListener('click', (e) => {
    if (e.target.id === 'qa-add-router-modal') {
      Modal.close();
    }
  });
  
  // Router form
  document.getElementById('qa-add-router-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    data.livereport = document.getElementById('livereport').checked;
    Router.addRouter(data);
  });
  
  // Search with debounce
  const debouncedSearch = Utils.debounce((query) => {
    Router.search(query);
  }, APP_CONFIG.SEARCH_DEBOUNCE);
  
  document.getElementById('qa-search-input').addEventListener('input', (e) => {
    debouncedSearch(e.target.value);
  });
});
