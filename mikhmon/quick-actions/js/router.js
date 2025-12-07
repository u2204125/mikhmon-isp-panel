/**
 * Router Management Module
 */

const Router = {
  /**
   * Load all routers
   */
  async loadRouters() {
    Utils.showLoader();
    try {
      const response = await API.getRouters();
      
      if (response.success) {
        APP_STATE.routers = response.data;
        APP_STATE.filteredRouters = response.data;
        this.renderRouters();
      }
    } catch (error) {
      Utils.showToast(error.message || 'Failed to load routers', 'error');
      if (error.message.includes('Unauthorized')) {
        Auth.logout();
      }
    } finally {
      Utils.hideLoader();
    }
  },

  /**
   * Render routers list
   */
  renderRouters() {
    const tableBody = document.getElementById('qa-routers-table-body');
    const cardsContainer = document.getElementById('qa-routers-cards');
    const emptyState = document.getElementById('qa-empty-state');
    
    if (APP_STATE.filteredRouters.length === 0) {
      tableBody.innerHTML = '';
      cardsContainer.innerHTML = '';
      emptyState.style.display = 'block';
      return;
    }
    
    emptyState.style.display = 'none';
    
    // Render table rows
    tableBody.innerHTML = APP_STATE.filteredRouters.map(router => `
      <tr>
        <td><strong>${Utils.escapeHtml(router.sessname)}</strong></td>
        <td><code>${Utils.escapeHtml(router.ipmik)}</code></td>
        <td>${Utils.escapeHtml(router.hotspotname)}</td>
        <td>${router.dnsname ? Utils.escapeHtml(router.dnsname) : '-'}</td>
        <td>
          <span class="qa-status-badge qa-status-active">
            <span class="qa-status-dot"></span>
            Active
          </span>
        </td>
      </tr>
    `).join('');
    
    // Render cards
    cardsContainer.innerHTML = APP_STATE.filteredRouters.map(router => `
      <article class="qa-router-card">
        <div class="qa-router-card-header">
          <div>
            <h3 class="qa-router-card-title">${Utils.escapeHtml(router.sessname)}</h3>
            <div class="qa-router-card-meta">${Utils.escapeHtml(router.ipmik)}</div>
          </div>
          <span class="qa-status-badge qa-status-active">
            <span class="qa-status-dot"></span>
            Active
          </span>
        </div>
        <div class="qa-router-card-body">
          <div class="qa-router-card-field">
            <div class="qa-router-card-field-label">Hotspot</div>
            <div class="qa-router-card-field-value">${Utils.escapeHtml(router.hotspotname)}</div>
          </div>
          <div class="qa-router-card-field">
            <div class="qa-router-card-field-label">DNS</div>
            <div class="qa-router-card-field-value">${router.dnsname ? Utils.escapeHtml(router.dnsname) : '-'}</div>
          </div>
          <div class="qa-router-card-field">
            <div class="qa-router-card-field-label">Interface</div>
            <div class="qa-router-card-field-value">${Utils.escapeHtml(router.iface)}</div>
          </div>
          <div class="qa-router-card-field">
            <div class="qa-router-card-field-label">Currency</div>
            <div class="qa-router-card-field-value">${Utils.escapeHtml(router.currency)}</div>
          </div>
        </div>
      </article>
    `).join('');
  },

  /**
   * Search routers
   */
  search(query) {
    const searchTerm = query.toLowerCase();
    APP_STATE.filteredRouters = APP_STATE.routers.filter(router => 
      router.sessname.toLowerCase().includes(searchTerm) ||
      router.ipmik.toLowerCase().includes(searchTerm) ||
      router.hotspotname.toLowerCase().includes(searchTerm) ||
      (router.dnsname && router.dnsname.toLowerCase().includes(searchTerm))
    );
    this.renderRouters();
  },

  /**
   * Add new router
   */
  async addRouter(formData) {
    Utils.showLoader();
    try {
      const response = await API.addRouter(formData);
      
      if (response.success) {
        Utils.showToast('Router added successfully');
        Modal.close();
        this.loadRouters();
      }
    } catch (error) {
      Utils.showToast(error.message || 'Failed to add router', 'error');
    } finally {
      Utils.hideLoader();
    }
  }
};

// Export Router
window.Router = Router;
