export function formatTextWithLineNumbers(text) {
  return (text || '')
    .split(/\r?\n\r?\n/)
    .map(s => s.trim())
    .filter(p => p !== '')
}
