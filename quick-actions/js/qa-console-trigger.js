(function () {
  // Expose a simple function to trigger the test endpoints from the browser console.
  // Usage from console: `await qaTriggerTest()`
  // Optional: `await qaTriggerTest({base: '/some/path/'} )` if index is served with a subpath.

  async function fetchJson(path) {
    const res = await fetch(path, {credentials: 'same-origin'});
    if (!res.ok) throw new Error(`HTTP ${res.status} when fetching ${path}`);
    return res.json();
  }

  function ensureOutputBox() {
    let el = document.getElementById('qa-console-trigger-output');
    if (!el) {
      el = document.createElement('pre');
      el.id = 'qa-console-trigger-output';
      el.style.position = 'fixed';
      el.style.right = '16px';
      el.style.bottom = '16px';
      el.style.maxWidth = '45%';
      el.style.maxHeight = '50%';
      el.style.overflow = 'auto';
      el.style.padding = '12px';
      el.style.background = 'rgba(0,0,0,0.85)';
      el.style.color = '#fff';
      el.style.borderRadius = '6px';
      el.style.zIndex = '99999';
      el.style.fontSize = '12px';
      el.style.lineHeight = '1.4';
      el.style.boxShadow = '0 6px 18px rgba(0,0,0,0.3)';
      el.textContent = 'QA Console Trigger - waiting for results...';
      document.body.appendChild(el);
    }
    return el;
  }

  window.qaTriggerTest = async function (opts) {
    opts = opts || {};
    const base = opts.base || '';
    const out = ensureOutputBox();
    out.textContent = 'Running QA test...\n';

    try {
      out.textContent += '\nFetching credential (masked by default)...\n';
      const cred = await fetchJson(base + 'test-credential-mock.php');
      out.textContent += JSON.stringify(cred, null, 2) + '\n';
      console.log('qaTriggerTest: credential', cred);

      out.textContent += '\nTriggering mock quick action...\n';
      const act = await fetchJson(base + 'mock_quick_action.php');
      out.textContent += JSON.stringify(act, null, 2) + '\n';
      console.log('qaTriggerTest: action', act);

      out.textContent += '\nDone. Use `qaTriggerTest({reveal: true})` to attempt reveal (reveal only works if server allows it).\n';

      return {credential: cred, action: act};
    } catch (err) {
      out.textContent += '\nError: ' + (err && err.message ? err.message : String(err)) + '\n';
      console.error('qaTriggerTest error', err);
      throw err;
    }
  };

  // Optional helper to fetch the reveal URL (if you run the page on localhost):
  window.qaRevealCredential = async function (opts) {
    opts = opts || {};
    const base = opts.base || '';
    return fetchJson(base + 'test-credential-mock.php?reveal=1');
  };

  // Friendly note in console when the script loads.
  if (typeof console !== 'undefined') {
    console.info('qa-console-trigger loaded: run `await qaTriggerTest()` from the browser console');
  }
})();
