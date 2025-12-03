/**
 * Modal Module
 */

const Modal = {
  /**
   * Open add router modal
   */
  open() {
    document.getElementById('qa-add-router-form').reset();
    document.getElementById('qa-add-router-modal').classList.add('qa-active');
  },

  /**
   * Close modal
   */
  close() {
    document.getElementById('qa-add-router-modal').classList.remove('qa-active');
  }
};

// Export Modal
window.Modal = Modal;
