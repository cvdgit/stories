export function decodeHtml(html) {
  const txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}

export function processOutputAsJson(output) {
  let json = null
  try {
    json = JSON.parse(output.replace(/```json\n?|```/g, ''))
  } catch (ex) {
    console.log(ex.message)
  }
  return json
}

export function stripTags(html) {
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.textContent || div.innerText || '';
}

export const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ");
