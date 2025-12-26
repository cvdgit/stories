(function() {

  document.querySelectorAll('.reveal').forEach(elem => {
    const deck = new Reveal(elem, {
      embedded: true,
      width: 1280,
      height: 720,
      margin: 0,
      transition: 'slide',
      transitionSpeed: 'default',
      backgroundTransition: 'fade',
      center: false,
      controls: false,
      controlsLayout: 'bottom-right',
      controlsBackArrows: 'faded',
      controlsTutorial: false,
      progress: false,
      keyboard: false,
      history: false,
      mouseWheel: false,
      showNotes: false,
      shuffle: false,
      loop: false,
      hash: true,
      hashOneBasedIndex: true,
      rtl: false,
      help: false,
      dependencies: [],
      touch: false,
      maxScale: 1.0,
      minScale: 0.8
    });
    deck.initialize();
  });
})();
