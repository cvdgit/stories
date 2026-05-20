window.SpeakSlideTextPlugin = (function() {
  const {speakSlideTextConfig: config} = Reveal.getConfig();

  const speakSlideText = new window.SpeakSlideText(Reveal, config);

  Reveal.addEventListener('slidechanged', () => {
    speakSlideText.init();
  });

  Reveal.addEventListener('ready', ({indexh, indexv}) => {
    if (Number(indexh) > 0 || Number(indexv) > 0) {
      return;
    }
    speakSlideText.init();
  });

  return {
    canNext() {
      return speakSlideText.canNext();
    }
  }
})();
