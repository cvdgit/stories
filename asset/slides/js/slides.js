import 'reveal.js/dist/reveal.css';
import '../css/wikids-reveal.css';
import Reveal from 'reveal.js';
import CustomControls from "./plugins/controls/CustomControls";
import Feedback from "./plugins/Feedback";
import Transition from "./plugins/Transition";
import Testing from "./plugins/Testing";
import LinksPlugin from "./plugins/links/LinksPlugin";
import VideoPlugin from "./plugins/video/VideoPlugin";
import ActionsPlugin from "./plugins/ActionsPlugin";
import SlideLinksPlugin from "./plugins/slide_links/SlideLinksPlugin";
import StatPlugin from "./plugins/stat/StatPlugin";
import SlidesPlayer from "./SlidesPlayer";
import Background from "./plugins/Background";
import NextStory from "./plugins/NextStory";
import isMobile from "./is_mobile";

function onSlideMouseDown(e, player) {

  e = e || window.event;

  const $target = $(e.target);

  if ($target.parents("section[data-slide-view=question]").length ||
    $target.parents("section[data-slide-view=new-question]").length ||
    $target.parents('.new-questions').length ||
    ($target[0].tagName === "IMG" && $target.attr("data-action") === "1") ||
    $target.hasClass("btn")||
    $target.parents(".wikids-slide-links").length ||
    $target.parents(".story-controls").length ||
    $target[0].tagName === "AUDIO" ||
    $target.hasClass("story-controls") ||
    ($target.hasClass("wikids-recorder") || $target.parents(".wikids-recorder").length) ||
    (($target.hasClass("plyr") || $target.parents(".plyr").length) && window["WikidsVideo"]) ||
    ($target[0].tagName === "A" && $target.parents('.slide-paragraph').length) ||
    ($target.hasClass("slide-state-alert-wrapper") || $target.parents(".slide-state-alert-wrapper").length) ||
    ($target.hasClass("slide-hints-wrapper") || $target.parents(".slide-hints-wrapper").length)
  )  {
    return;
  }
  switch (e.which) {
    case 1: player.right(); break;
    case 3: player.left(); break;
  }
}

window.initSlides = function() {

  const deck = new Reveal(document.querySelector('[data-toggle=slides]'), {
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
    minScale: 0.8
  });

  deck.initialize({
    plugins: [
      CustomControls,
      Feedback,
      Transition,
      Testing,
      LinksPlugin,
      VideoPlugin,
      ActionsPlugin,
      SlideLinksPlugin,
      StatPlugin,
      Background,
      NextStory
    ]
  });

  const slidesPlayer = new SlidesPlayer(deck);

  deck.on('ready', function() {

    if (!isMobile()) {
      deck.on("mousedown", (e) => onSlideMouseDown(e, slidesPlayer));
      deck.on("contextmenu", (e) => e.preventDefault());
    }
  })

  return deck;
}
