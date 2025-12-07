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
    // prevent background scrolling while modal is open
    try { document.body.classList.add('qa-modal-open'); } catch(e){}
  },

  /**
   * Close modal
   */
  close() {
    document.getElementById('qa-add-router-modal').classList.remove('qa-active');
    try { document.body.classList.remove('qa-modal-open'); } catch(e){}
  }
};

// Export Modal
window.Modal = Modal;
