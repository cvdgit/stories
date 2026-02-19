(function() {
  const {tableOfContentsConfig: config} = Reveal.getConfig();
  const instance = window.TableOfContents;
  Reveal.addEventListener('slidechanged', () => {
    instance.initDeckEvent(Reveal, config);
  });
  Reveal.addEventListener('ready', ({indexh, indexv}) => {
    if (Number(indexh) > 0 || Number(indexv) > 0) {
      return;
    }
    instance.initDeckEvent(Reveal, config);
  });
})();
