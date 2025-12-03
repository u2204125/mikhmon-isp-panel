(function(){
  // Small test client that fetches the credential JSON and runs the mock action.
  // Drop this into the `quick-actions` page for quick local testing.
  const out = document.createElement('pre');
  out.id = 'qa-test-output';
  out.style.padding = '1rem';
  out.style.background = '#f4f4f4';
  out.style.border = '1px solid #ddd';
  out.style.whiteSpace = 'pre-wrap';
  out.style.fontSize = '13px';
  document.body.appendChild(out);

  async function run() {
    try {
      const r = await fetch('test-credential-mock.php');
      const j = await r.json();
      out.textContent = 'test-credential-mock.php response:\n' + JSON.stringify(j, null, 2);
      console.log('test-credential-mock', j);

      const r2 = await fetch('mock_quick_action.php');
      const j2 = await r2.json();
      out.textContent += '\n\nmock_quick_action.php response:\n' + JSON.stringify(j2, null, 2);
      console.log('mock_quick_action', j2);
    } catch (e) {
      out.textContent = 'Error fetching mock endpoints: ' + e;
      console.error(e);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();
