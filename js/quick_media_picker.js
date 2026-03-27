document.addEventListener('click', function (e) {
  const el = e.target.closest('.quick-media-item');
  if (!el) return;

  const token = el.getAttribute('data-token');
  if (!token) return;

  // Fallback copy method (works without clipboard API)
  const temp = document.createElement('textarea');
  temp.value = token;

  document.body.appendChild(temp);
  temp.select();
  document.execCommand('copy');
  document.body.removeChild(temp);
});
