
function onStoryChange(storyId) {
  if (!storyId) {
    return;
  }
  sendSlidesRequest(storyId);
}

function makeReveal(elem) {
  const deck = new Reveal(elem, {
    embedded: true
  });
  deck.initialize({
    'width': 1280,
    'height': 720,
    'margin': 0.01,
    'transition': 'none',
    'disableLayout': true,
    'controls': false,
    'progress': false,
    'slideNumber': false
  });
  return deck;
}

function sendSlidesRequest(storyId) {
  const $importList = $('#import-slides-list');
  $('#import-slides-count').text('0');
  $importList.empty();
  $.getJSON(`/admin/index.php?r=slide/slides&story_id=${storyId}`)
    .done(slides => {
      slides.forEach(slide => {
        const slideElem = $(drawSlide(slide));
        $importList.append(slideElem);
        makeReveal(slideElem.find('.reveal')[0]);
      });
    });
}

function drawSlide(slide) {
  return `
    <div class="import-slides-row">
        <div class="thumb-reveal-wrapper">
            <div class="thumb-reveal-inner">
                <div class="thumb-reveal reveal" style="width: 1280px; height: 720px; transform: scale(0.28)">
                    <div class="slides">${slide.data}</div>
                </div>
            </div>
            <div class="thumb-reveal-options">123</div>
            <div class="thumb-reveal-info">
                <div title="${slide.story}" class="option slide-number">${slide.slideNumber}</div>
            </div>
        </div>
        <div class="slides-row-actions">
            <div class="checkbox">
                <label style="padding-left: 0; padding-right: 20px">
                    Выбрать <input style="margin-left: 10px" name="slides" type="checkbox" value="${slide.id}">
                </label>
            </div>
        </div>
    </div>
  `;
}

$('#import-slides').on('click', function() {

  const fromStoryId = $('#select-story-slides option:selected').val();
  if (!fromStoryId) {
    return;
  }

  const ids = $('#import-slides-list .import-slides-row .slides-row-actions input[type=checkbox]:checked').map((i, elem) => {
    return $(elem).val();
  }).get();
  if (!ids.length) {
    return;
  }

  const deleteSlides = $('#import-slides-delete').is(':checked') ? '1' : '0';

  if (!confirm(`Подтверждаете импорт${deleteSlides === '1' ? ' и удаление выбранных слайдов из выбранной истории ' : ''} слайдов?`)) {
    return;
  }

  const toStoryId = $('#import-slides-list')
    .attr('data-to-story-id');

  const formData = new FormData();
  formData.append('to_story_id', toStoryId);
  formData.append('from_story_id', fromStoryId);
  formData.append('delete_slides', deleteSlides);
  ids.map(slideId => formData.append('slides[]', slideId));

  sendForm(`/admin/index.php?r=slide-import/import&story_id=${fromStoryId}`, 'post', formData)
    .done(response => {
      if (response && response.success) {
        toastr.success(response.message);
        location.href = `/admin/index.php?r=editor/edit&id=${toStoryId}`;
      }
      if (response && response.success === false) {
        toastr.error(response.message);
      }
    });
});

$('#import-slides-list').on('click', '.slides-row-actions input[type=checkbox]', function() {
  $('#import-slides-count').text($('#import-slides-list .slides-row-actions input[type=checkbox]:checked').length);
});
