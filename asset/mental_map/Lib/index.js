export function formatTextWithLineNumbers(text) {
  return (text || '')
    .split(/\r?\n\r?\n/)
    .map(s => s.trim())
    .filter(p => p !== '')
}

export function stripTags(html) {
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.textContent || div.innerText || '';
}
