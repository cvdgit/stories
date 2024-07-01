(function() {

  const readySlides = [];

  const mentalMapBuilder = new MentalMapManagerQuiz()

  function getCurrentSlide() {
    return Reveal.getCurrentSlide();
  }

  function init() {
    const elem = $('div.mental-map', getCurrentSlide());
    if (!elem.length) {
      return
    }

    const mentalMapId = elem.attr('data-mental-map-id')
    if (!mentalMapId) {
      throw new Error('Mental map id not found')
    }

    const mentalMap = mentalMapBuilder.create(elem[0], {
      init: async () => {
        const response = await fetch(`/mental-map/init`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
          },
          body: JSON.stringify({
            id: mentalMapId
          })
        })

        const json = await response.json()
        return json.mentalMap
      }
    })
    mentalMap.run()
  }

  function initMentalMap() {
    const currentSlideID = $(getCurrentSlide()).attr('data-id')
    if (readySlides[currentSlideID]) {
      return;
    }
    readySlides[currentSlideID] = true;
    console.log('mental map init')
    init();
  }

  Reveal.addEventListener('slidechanged', initMentalMap)
  Reveal.addEventListener('ready', initMentalMap)
})()
