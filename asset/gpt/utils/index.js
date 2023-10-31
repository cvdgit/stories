export function formatNumber(n) {
  return n < 10 ? `0${n}` : n;
}

export function dateFormat(ms) {
  const date = new Date(parseInt(ms));

  const year = date.getFullYear();
  let month = date.getMonth() + 1;
  let day = date.getDate();

  month = formatNumber(month);
  day = formatNumber(day);

  let hour = date.getHours();
  let minute = date.getMinutes();
  let second = date.getSeconds();

  hour = formatNumber(hour);
  minute = formatNumber(minute);
  second = formatNumber(second);

  return [[year, month, day].join("/"), [hour, minute, second].join(":")].join(
    " "
  );
}

export function uuidv4() {
  return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  );
}
