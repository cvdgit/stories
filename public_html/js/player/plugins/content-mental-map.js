(function() {

  const readySlides = [];

  function getCurrentSlide() {
    return Reveal.getCurrentSlide();
  }

  const {contentMentalMapConfig: config} = Reveal.getConfig()

  function InnerDialog(title, content) {
    const html = `<div class="slide-hints-wrapper" style="background-color: white">
    <div class="retelling-dialog-inner">
        <div class="retelling-dialog-header">
            <h2>${title}</h2>
            <div class="header-actions">
                <button type="button" class="hints-close">&times;</button>
            </div>
        </div>
        <div id="dialog-body" class="retelling-dialog-body"></div>
    </div>
</div>
`
    this.showHandler = null
    this.hideHandler = null

    const hideDialog = () => {

      if (typeof this.hideHandler === "function") {
        this.hideHandler()
      }

      Reveal.configure({keyboard: true})

      if ($(Reveal.getCurrentSlide()).find('.slide-hints-wrapper').length) {
        $(Reveal.getCurrentSlide())
          .find('.slide-hints-wrapper')
          .hide()
          .remove()
      }
      $('.reveal .story-controls').show()
    }

    this.show = () => {

      Reveal.configure({keyboard: false})

      const $element = $(html)

      $element.find("#dialog-body").append(content)
      $element.on("click", ".hints-close", hideDialog)

      $('.reveal .story-controls').hide()
      $('.reveal .slides section.present')
        .append($element)
        .find(".slide-hints-wrapper")
        .show()

      if (typeof this.showHandler === "function") {
        this.showHandler($element)
      }
    }

    this.hide = hideDialog;

    this.onShow = callback => {
      this.showHandler = callback
    }

    this.onHide = callback => {
      this.hideHandler = callback
    }
  }

  function showMentalMapDialog(mentalMapId, onHideHandler) {
    const content = `<div class="retelling-content">
<div class="mental-map" style="text-align: left"></div>
<div class="mental-map-loader"></div>
</div>`

    const feedbackDialog = new InnerDialog('', content)

    let mentalMap

    feedbackDialog.onShow(async ($element) => {
      mentalMap = mentalMapBuilder.create($element.find('.mental-map')[0], Reveal, {
        init: async () => {
          const response = await fetch(`/mental-map/init`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            },
            body: JSON.stringify({
              story_id: config.story_id,
              id: mentalMapId
            })
          })

          const json = await response.json()
          return {...json}
        },
        ...{
          story_id: config.story_id,
          mentalMapId,
        }
      }, $(getCurrentSlide()).attr('data-id'))
      await mentalMap.run()
      $element.find('.mental-map-loader').fadeOut().remove()
    })

    feedbackDialog.onHide(() => {
      mentalMap.destroy()
      onHideHandler(mentalMap.getUserProgress())
    })

    feedbackDialog.show()
  }

  function init() {

    const slideId = Number($(getCurrentSlide()).attr('data-id'))
    const mentalMapsData = config.mentalMaps.find(m => m.slideId === slideId)

    if (!mentalMapsData) {
      return
    }

    const wrap = $('<div class="content-mm-wrap"><div class="content-mm-inner"></div></div>')
    wrap.find('.content-mm-inner').append('<h3 style="margin: 0; text-align: left">Речевой тренажёр:</h3>')

    mentalMapsData.mentalMaps.map(mentalMap => {
      const elem = $(`<div data-mm-id="${mentalMap.id}" class="content-mm-item"><div class="content-mm-name"></div><div class="content-mm-progress"></div></div>`)
      elem.find('.content-mm-name')
        .text(mentalMap.name)
        .end()
        .on('click', e => {
          showMentalMapDialog(mentalMap.id, (progress) => elem.find('.content-mm-progress').text(`(${progress}%)`))
        })
      elem.find('.content-mm-progress').text(`(${mentalMap.userProgress}%)`)
      wrap.find('.content-mm-inner').append(elem)
    })

    $(getCurrentSlide()).append(wrap)
  }

  function initMentalMap() {
    const currentSlideID = $(getCurrentSlide()).attr('data-id')
    if (readySlides[currentSlideID]) {
      return;
    }
    readySlides[currentSlideID] = true;
    init();
  }

  Reveal.addEventListener('slidechanged', initMentalMap)
  Reveal.addEventListener('ready', initMentalMap)
})()
