
const retellingBuilder = window.retellingBuilder = new RetellingManagerQuiz();


(function() {

  const readySlides = [];

  function getCurrentSlide() {
    return Reveal.getCurrentSlide();
  }

  const {retellingConfig} = Reveal.getConfig()

  function init() {
    const elem = $('div.retelling-block', getCurrentSlide());
    if (!elem.length) {
      return
    }

    const retellingId = elem.attr('data-retelling-id')
    if (!retellingId) {
      throw new Error('Retelling id not found')
    }

    const retelling = retellingBuilder.create(elem[0], Reveal, {
      init: async () => {
        const response = await fetch(`/retelling/init`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
          },
          body: JSON.stringify({
            story_id: retellingConfig.story_id,
            slide_id: $(getCurrentSlide()).attr('data-id'),
            id: retellingId
          })
        })

        const json = await response.json()
        return {...json}
      },
      ...retellingConfig,
      ...elem.data()
    }, $(getCurrentSlide()).attr('data-id'), MicrophoneChecker)
    retelling.run()
  }

  function initRetelling() {
    const currentSlideID = $(getCurrentSlide()).attr('data-id')
    if (readySlides[currentSlideID]) {
      return;
    }
    readySlides[currentSlideID] = true;
    init();
  }

  Reveal.addEventListener('slidechanged', () => {
    initRetelling();
    retellingBuilder.destroyInstances();
  });
  Reveal.addEventListener('ready', ({indexh, indexv}) => {
    if (Number(indexh) > 0 || Number(indexv) > 0) {
      return;
    }
    initRetelling();
  })
})()
