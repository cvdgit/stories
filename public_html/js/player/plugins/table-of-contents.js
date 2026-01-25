window.TableOfContentsPlugin = (function() {

  const {tableOfContentsConfig: config} = Reveal.getConfig();
  const {storyId, userId} = config;

  const getDashProps = (progress) => {

    const size = 16;
    const center = size / 2,
      radius = 7;

    const dashArray = 2 * Math.PI * radius;
    return {
      dashArray,
      dashOffset: dashArray * ((100 - progress) / 100)
    };
  }

  const LessonCircleProgress = (progress) => {

    const done = progress >= 100;
    const dash = getDashProps(progress);

    return `
    <span class="lesson-progress-circle">
      <svg xmlns="https://www.w3.org/2000/svg" fill="none" focusable="false" role="img" viewBox="0 0 16 16" class="svg-pi" style="width: 100%; height: 100%; overflow: visible">
        <circle
          class="svg-pi-track"
          cx="8"
          cy="8"
          fill="transparent"
          r="7"
          stroke-width="2"
        />
        <circle
            class="svg-pi-indicator ${done ? 'lesson-progress-circle__done' : ''}"
            cx="8"
            cy="8"
            fill="transparent"
            r="7"
            stroke-width="2"
            stroke-dasharray=${dash.dashArray}
            stroke-dashoffset=${dash.dashOffset}
            transform="rotate(-89.9, 8, 8)"
          />
          <path class="lesson-progress-circle__pass ${done ? 'lesson-progress-circle__pass--done lesson-progress-circle__pass--visible' : ''}" d="M11.3227 6.65905C11.6133 6.37599 11.6347 5.89413 11.3705 5.58277C11.1063 5.27141 10.6566 5.24847 10.366 5.53152L6.93323 8.87512L5.6338 7.60944C5.3432 7.32639 4.89345 7.34933 4.62927 7.66069C4.36509 7.97205 4.38651 8.45391 4.67711 8.73697L6.45488 10.4686C6.72611 10.7328 7.14034 10.7328 7.41157 10.4686L11.3227 6.65905Z"></path>
      </svg>
    </span>
  `;
  };

  const UpdateLessonCircleProgress = (elem, progress) => {

    const done = progress >= 100;
    const dash = getDashProps(progress);

    elem.querySelector('.svg-pi-indicator').setAttribute('stroke-dasharray', dash.dashArray);
    elem.querySelector('.svg-pi-indicator').setAttribute('stroke-dashoffset', dash.dashOffset);

    if (done) {
      elem.querySelector('.svg-pi-indicator').classList.add('lesson-progress-circle__done');
      elem.querySelector('.lesson-progress-circle__pass').classList.add('lesson-progress-circle__pass--done');
      elem.querySelector('.lesson-progress-circle__pass').classList.add('lesson-progress-circle__pass--visible');
    }
  }

  function getCurrentSlide() {
    return Reveal.getCurrentSlide();
  }

  async function fetchUserHistory(storyId, userId) {
    const response = await fetch(`/story/history-by-slide?storyId=${storyId}&userId=${userId}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      },
    })
    if (!response.ok) {
      throw new Error(response.statusText)
    }
    return await response.json()
  }

  function init(payload, container) {

    if (container.find('.table-of-contents-inner').length) {
      container.find('.table-of-contents-inner').remove();
    }

    const $inner = $(`<div class="table-of-contents-inner">
    <h2 class="table-of-contents-header" style="text-align: center">${payload.title || 'Оглавление'}</h2>
    <div class="table-of-contents-content"></div>
</div>`);

    container.append($inner);

    const slidesMap = new Map();
    $('.story-container-wrap .reveal .slides section').each((i, el) => {
      const $el = $(el);
      slidesMap.set(Number($el.attr('data-id')), $el.index() + 1);
    });

    createContent(
      container.find('.table-of-contents-content'),
      payload,
      slidesMap
    );

    fetchUserHistory(storyId, userId)
      .then(response => {
        if (!response.success) {
          return;
        }

        const cardsMap = new Map();
        payload.groups.map(({cards, slides}) => {
          cards.map(({id}) => {
            const cardSlides = slides.filter(s => s.cardId === id);
            if (!cardSlides.length) {
              cardsMap.set(id, 100);
              return;
            }
            const progress = cardSlides.reduce((total, cardSlide) => {
              const slide = response.data.find(({slideId}) => Number(slideId) === Number(cardSlide.id));
              return total + Number(slide.progress);
            }, 0);
            cardsMap.set(id, Math.round(progress / cardSlides.length));
          });
        });

        cardsMap.forEach((progress, cardId) => {
          const $elem = container
            .find(`.table-of-contents-content [data-group-card-id='${cardId}'] .lesson-progress-circle`);
          if ($elem.length) {
            UpdateLessonCircleProgress($elem[0], progress);
          }
        });
      });
  }

  function initTableOfContents() {

    if ($('.story-container-wrap .table-of-contents-caption').length) {
      $('.story-container-wrap .table-of-contents-caption').remove();
    }

    if ($(getCurrentSlide()).find(`[data-block-type='table-of-contents']`).length > 0) {
      const payload = JSON.parse($(getCurrentSlide()).find('.table-of-contents-payload').text());
      init(payload, $(getCurrentSlide()).find('.table-of-contents'));
      return;
    }

    const $prevTableOfContentsSlide = $(getCurrentSlide()).prevAll(`[data-slide-view='table-of-contents']:eq(0)`);
    if (!$prevTableOfContentsSlide.length) {
      return;
    }

    if ($prevTableOfContentsSlide.find('.table-of-contents').length) {

      const payload = JSON.parse(
        $prevTableOfContentsSlide.find('.table-of-contents > .table-of-contents-payload').text()
      );

      const $title = $(
        `<div class="table-of-contents-caption">
<div class="caption-group-card-index contents-index">${payload.title}</div>
</div>`
      );

      const id = Number($(getCurrentSlide()).attr('data-id'));

      for (const group of payload.groups) {
        const $group = $('<div class="caption-group"/>');
        group.cards.map(({id, name}) => {
          const $card = $(`<div class="caption-group-card" data-group-card-id="${id}" />`)
            .text(name)
            .on('click', ({target}) => {
              if ($(target).hasClass('active-slide')) {
                return;
              }
              const cardSlides = group.slides.filter(s => s.cardId === id);
              location.hash = '#/' + ($('.story-container-wrap .slides').find(`section[data-id=${cardSlides[0].id}]`).index() + 1)
            });
          $group.append($card);
        });
        $title.append($group);
      }

      let activeCardId;
      for (const group of payload.groups) {
        const slide = group.slides.find(s => Number(s.id) === id);
        if (slide) {
          const card = group.cards.find(c => c.id === slide.cardId);
          if (card) {
            activeCardId = card.id;
          }
          break;
        }
      }

      if (activeCardId) {
        $title.find(`[data-group-card-id='${activeCardId}']`).addClass('active-slide');
      }

      $title.find('.contents-index').on('click', () => location.hash = '#/' + ($prevTableOfContentsSlide.index() + 1));
      $('.story-container-wrap').prepend($title);
    }
  }

  function createContent($container, payload, slidesMap) {
    let i = 1;
    (payload.groups || []).map(({name, slides, cards}) => {

      $container.append(
        `<div style="margin-bottom: 16px; font-size: 16px; line-height: 24px; font-weight: 700;">${name}</div>`
      );

      const $row = $(`<div style="display: grid; grid-auto-flow: column; align-items: center; overflow-x: auto; grid-auto-columns: 200px; gap: 4px; margin-bottom: 30px;"></div>`);

      (cards || []).map(({id, name}) => {

        const cardSlides = slides.filter(s => s.cardId === id);
        if (cardSlides.length === 0) {
          return;
        }

        const slideIds = cardSlides.map(s => s.id).join(',');

        const $slide = $(`<div data-group-card-id="${id}" data-slide-id="${slideIds}" class="slide-content-wrap">
<div style="position: relative; width: 50px; margin-bottom: 16px;">
<svg xmlns="http://www.w3.org/2000/svg" width="51" height="32" fill="none" style="color: rgb(244, 242, 238)"><path fill="currentColor" d="M33.62 1.252A6 6 0 0 0 29.953 0H6a6 6 0 0 0-6 6v20a6 6 0 0 0 6 6h23.953a6 6 0 0 0 3.667-1.252l16.02-12.374a3 3 0 0 0 0-4.748z"></path></svg>
<span style="font-size: 14px; line-height: 32px; top: 0; left: 14px; position: absolute">${i}</span>
</div>
<div style="font-size: 14px; line-height: 20px">${name}</div>
</div>`);

        const slideNumber = slidesMap.get(Number(cardSlides[0].id));
        $slide.on('click', () => {
          location.hash = `#/${slideNumber}`;
        });

        $slide.append(
          `<div style="position: absolute; top: 11px; right: 11px; width: 40px; height: 40px">${LessonCircleProgress(0)}</div>`
        );

        $row.append($slide);

        i++;
      });
      $container.append($row);
    });
  }

  Reveal.addEventListener('slidechanged', () => {
    initTableOfContents();
  });
  Reveal.addEventListener('ready', ({indexh, indexv}) => {
    if (Number(indexh) > 0 || Number(indexv) > 0) {
      return;
    }
    initTableOfContents();
  });

  return {
    init,
    initEdit(payload, container, slidesMap) {

      if (container.find('.table-of-contents-inner').length) {
        container.find('.table-of-contents-inner').remove();
      }

      const $inner = $(`<div class="table-of-contents-inner" style="width: 100%">
        <h1 class="table-of-contents-header" style="text-align: center; margin-bottom: 20px;">
            ${payload.title || 'Оглавление'}
            <button class="table-of-contents-edit" type="button" style="pointer-events: all !important;">
                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-6" style="margin-left: 10px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                </svg>
            </button>
        </h1>
        <div class="table-of-contents-content"></div>
</div>`);

      container.append($inner);

      createContent(
        container.find('.table-of-contents-content'),
        payload,
        slidesMap
      );
    }
  }
})();
