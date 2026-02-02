(function() {

  function initPlugin() {
    const $slide = $(Reveal.getCurrentSlide());
    $slide.find(`[data-block-type='text']`).each((i, el) => {
      const {top} = el.getBoundingClientRect();
      console.log(top, el.style.top);
      el.style.maxHeight = `calc(100% - ${top}px)`;
    });
  }

  Reveal.addEventListener('slidechanged', () => {
    initPlugin();
  });
  Reveal.addEventListener('ready', ({indexh, indexv}) => {
    if (Number(indexh) > 0 || Number(indexv) > 0) {
      return;
    }
    initPlugin();
  });
})();
