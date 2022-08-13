import 'reveal.js/dist/reveal.css';
import '../css/wikids-reveal.css';
import Reveal from 'reveal.js';

window.initSlides = function() {
  let deck1 = new Reveal(document.querySelector('[data-toggle=slides]'), {
    embedded: true,
    width: 1280,
    height: 720,
    margin: 0.01,
    transition: 'none',
    backgroundTransition: 'slide',
    center: false,
    controls: false,
    controlsLayout: 'bottom-right',
    controlsBackArrows: 'faded',
    controlsTutorial: false,
    progress: true,
    history: false,
    mouseWheel: false,
    showNotes: false,
    slideNumber: false,
    shuffle: false,
    loop: false,
    hash: false,
    hashOneBasedIndex: true,
    rtl: false,
    help: false,
    dependencies: [],
    touch: true,
    maxScale: 1.0,
  });
  deck1.initialize();
}
