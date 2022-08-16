
function toUnixTS(ts) {
  return (new Date(ts).getTime() / 1000).toFixed(0);
}

function makeSessionId() {
  let text = "";
  const possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  for (let i = 0; i < 32; i++) {
    text += possible.charAt(Math.floor(Math.random() * possible.length));
  }
  return text;
}

export default (config) => {

  const session = makeSessionId();

  function send(data) {
    $.ajax({
      url: config.action,
      type: 'POST',
      dataType: 'json',
      data: data
    });
  }

  function getStatistics(ev) {

    if (!ev.previousSlide) {
      return null;
    }

    return {
      story_id: config.story_id,
      slide_id: $(ev.previousSlide).attr("data-id"),
      student_id: config.student_id,
      session
    };
  }

  function sendStatistics(ev) {

    const data = getStatistics(ev);

    if (data) {
      send(data);
    }
  }

  return {
    slideChangeEvent(ev) {

      const ts = toUnixTS(ev.timeStamp);

      if (ts > 0 && ev.indexh > 0) {
        sendStatistics(ev);
      }
    }
  };
};
