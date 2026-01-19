window.TableOfContentsPlugin = (function() {

  const {tableOfContentsConfig: config} = Reveal.getConfig();
  const {edit: editMode} = config;

  function getCurrentSlide() {
    return Reveal.getCurrentSlide();
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
        `<div class="table-of-contents-caption">${payload.title}</div>`
      );
      $title.on('click', () => location.hash = '#/' + ($prevTableOfContentsSlide.index() + 1));
      $('.story-container-wrap').prepend($title);
    }
  }

  function createContent($container, payload, slidesMap) {
    (payload.groups || []).map(({name, slides}) => {

      $container.append(
        `<div style="margin-bottom: 16px; font-size: 16px; line-height: 24px; font-weight: 700;">${name}</div>`
      );

      const $row = $(`<div style="display: grid; grid-auto-flow: column; align-items: center; overflow-x: auto; grid-auto-columns: 200px; gap: 4px; margin-bottom: 30px;"></div>`)
      slides.map(({id, title}) => {

        const slideNumber = slidesMap.get(id);

        const $slide = $(`<div class="slide-content-wrap">
<div style="position: relative; width: 50px; margin-bottom: 16px;">
<svg xmlns="http://www.w3.org/2000/svg" width="51" height="32" fill="none" style="color: rgb(244, 242, 238)"><path fill="currentColor" d="M33.62 1.252A6 6 0 0 0 29.953 0H6a6 6 0 0 0-6 6v20a6 6 0 0 0 6 6h23.953a6 6 0 0 0 3.667-1.252l16.02-12.374a3 3 0 0 0 0-4.748z"></path></svg>
<span style="font-size: 14px; line-height: 32px; top: 0; left: 14px; position: absolute">${slideNumber}</span>
</div>
<div style="font-size: 14px; line-height: 20px">${title}</div>
</div>`);
        $slide.on('click', e => {
          location.hash = `#/${slideNumber}`;
        });
        $row.append($slide);
      });
      $container.append($row);
    });
  }

  Reveal.addEventListener('slidechanged', initTableOfContents);
  Reveal.addEventListener('ready', initTableOfContents);

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
