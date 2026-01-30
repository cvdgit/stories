import SlidesConfig from "../SlidesConfig";
import "./ContentMentalMap.css";
import SlidesPlayer from "../SlidesPlayer";

let readySlides = [];

export default function ContentMentalMap() {

  const PluginId = 'content-mental-map';
  const config = new SlidesConfig().get(PluginId);

  function InnerDialog(title, content, deck) {
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

      deck.configure({keyboard: true, progress: true})

      if ($(deck.getCurrentSlide()).find('.slide-hints-wrapper').length) {
        $(deck.getCurrentSlide())
          .find('.slide-hints-wrapper')
          .hide()
          .remove()
      }
      $('.reveal .story-controls').show()
    }

    this.show = () => {

      deck.configure({keyboard: false, progress: false});

      const $element = $(html);

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

  function showMentalMapDialog(mentalMapId, onHideHandler, deck, config) {
    const content = `<div class="retelling-content">
<div class="mental-map" style="text-align: left"></div>
</div>`

    const feedbackDialog = new InnerDialog('', content, deck);

    let mentalMap

    feedbackDialog.onShow(async ($element) => {
      mentalMap = window.mentalMapBuilder.create($element.find('.mental-map')[0], deck, {
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
      }, $(deck.getCurrentSlide()).attr('data-id'))
      await mentalMap.run()
    })

    feedbackDialog.onHide(() => {
      mentalMap.destroy()
      onHideHandler(mentalMap.getUserProgress())
    })

    feedbackDialog.show()
  }

  function findSlideMentalMaps(slideId) {
    return config.mentalMaps.find(m => m.slideId === slideId);
  }

  const ArrowRightKeyCode = 39;

  function init(deck) {

    const slideId = Number($(deck.getCurrentSlide()).attr('data-id'));
    const mentalMapsData = config.mentalMaps.find(m => m.slideId === slideId);
    if (!mentalMapsData) {
      return
    }

    deck.addKeyBinding(
      {keyCode: ArrowRightKeyCode, key: 'ArrowRight', description: 'ArrowRight'},
      () => new SlidesPlayer(deck).right()
    );

    const {mapOrder} = config;

    const wrap = $('<div class="content-mm-wrap"><div class="content-mm-inner"></div></div>');

    const haveRequiredContents = mentalMapsData.mentalMaps.filter(m => m.required).length > 0;
    wrap.find('.content-mm-inner')
      .append(
        `<h3 class="content-mm-header">${haveRequiredContents ? `<strong title="Есть обязательный контент">*</strong> ` : ''}Речевой тренажёр</h3>`
      );

    mapOrder.map(type => {
      const mentalMap = mentalMapsData.mentalMaps.find(m => m.type === type);
      if (!mentalMap) {
        return;
      }

      const {id, required, name, userProgress, edit} = mentalMap;

      const elem = $(`
<div data-mm-id="${id}" class="content-mm-item">
<div class="content-mm-name">${required ? `<strong title="Обязательна для прохождения">*</strong> ` : ''}${name}</div>
<div class="content-mm-progress">(${userProgress}%)</div>
</div>`);
      elem.find('.content-mm-name')
        .on('click', e => {
          showMentalMapDialog(
            id,
            (progress) => {
              elem.find('.content-mm-progress').text(`(${progress}%)`);
              mentalMap.userProgress = progress;
            },
            deck,
            config
          )
        })
      if (edit) {
        elem.append(`<div><a title="Редактор" target="_blank" href="${edit.url}"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="margin-left: 10px;"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path></svg></a></div>`);
      }
      wrap.find('.content-mm-inner').append(elem)
    });

    $(deck.getCurrentSlide()).append(wrap);
  }

  function initMentalMap(deck) {
    const currentSlideID = Number($(deck.getCurrentSlide()).attr('data-id'));
    const slideMentalMaps = findSlideMentalMaps(currentSlideID);
    if (!slideMentalMaps) {
      return true;
    }

    deck.removeKeyBinding(ArrowRightKeyCode);

    if (readySlides[currentSlideID]) {

      deck.addKeyBinding(
        { keyCode: ArrowRightKeyCode, key: 'ArrowRight', description: 'ArrowRight' },
        () => new SlidesPlayer(deck).right()
      );

      return;
    }
    readySlides[currentSlideID] = true;
    init(deck);
  }

  return {
    id: PluginId,
    init(deck) {
      deck.addEventListener('slidechanged', () => {
        initMentalMap(deck);
      });
      deck.addEventListener('ready', ({indexh, indexv}) => {
        if (Number(indexh) > 0 || Number(indexv) > 0) {
          return;
        }
        initMentalMap(deck);
      });
    },
    canNext(slideId) {
      const slideMentalMaps = findSlideMentalMaps(slideId);
      if (!slideMentalMaps) {
        return true;
      }
      let canNext = true;
      const requiredMaps = slideMentalMaps
        .mentalMaps
        .filter(m => m.required);
      if (requiredMaps.length === 0) {
        return canNext;
      }
      return requiredMaps.reduce((totalDone, currentMap) => {
        return totalDone && currentMap.userProgress === 100;
      }, true);
    }
  }
}
