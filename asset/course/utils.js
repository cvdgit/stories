function uuidv4() {
  return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  );
}

function fetchJsonPost(url, body) {
  return fetch(url, {
    method: 'post',
    body: JSON.stringify(body),
    cache: 'no-cache',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
    }
  })
    .then((response) => {
      if (response.ok) {
        return response.json();
      }
      throw new Error(response.statusText);
    });
}

export {
  uuidv4,
  fetchJsonPost
};
