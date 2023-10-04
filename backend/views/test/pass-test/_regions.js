
const Modal = function({id, title}) {

  const content = `
    <div class="modal rounded-0 fade" tabindex="-1" id="${id}" data-backdrop="static">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="display: flex; justify-content: space-between">
            <h5 class="modal-title" style="margin-right: auto">${title}</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body d-flex">...</div>
        </div>
      </div>
    </div>
    `;

  if ($('body').find(`div#${id}`).length) {
    $('body').find(`div#${id}`).remove();
  }

  $('body').append(content);

  this.element = $('body').find(`div#${id}`);

  this.element
    .off('show.bs.modal')
    .on('show.bs.modal', () => {});
    //.off('hide.bs.modal');
    //.on('hide.bs.modal', hideCallback);

  this.show = ({body}) => {
    this.element.find('.modal-body')
      .empty()
      .append(body);
    this.element.modal();
  };

  this.hide = () => {
    this.element.modal('hide');
  }
}

const modal = new Modal({id: 'region-fragment', title: 'Выбор области'});

const initRegionFragments = ({testingId, fragment}) => {

  if (fragment.region.image !== null) {
    modal.show({
      body: createRegionEditor(fragment)
    });
    return;
  }

  modal.show({body: createRegionImageSelect(testingId, fragment)});
};

function createRegionImageSelect(testingId, fragment) {

  const body = `
    <div>
      <div style="padding-bottom: 20px">
        <form id="region-image-form" action="/admin/index.php?r=test/pass-test/region-image-upload" method="post" enctype="multipart/form-data">
          <input type="file" id="region-image-file" name="image">
          <input type="hidden" name="fragment_id" value="${fragment.id}">
          <input type="hidden" name="testing_id" value="${testingId}">
        </form>
        <div id="loading" style="display: none">
            <div style="width: 100%; height: 100%; min-height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column">
                <img width="45px" src="/img/loading.gif" alt="Loading...">
            </div>
        </div>
      </div>
      <div id="image-list" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px"></div>
    </div>
  `;

  const $body = $(body);
  const $form = $body.find('#region-image-form');

  $form.on('submit', e => {
    e.preventDefault();

    $body.find('#loading').show();

    $.ajax({
      url: $(e.target).attr('action'),
      type: $(e.target).attr('method'),
      data: new FormData(e.target),
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false
    })
      .done(response => {
        $body.find('#loading').hide();
        if (response.success) {
          fragment.region.image = response.data;
          $body.empty().append(createRegionEditor(fragment));
        } else {
          toastr.error(response.message);
        }
      });
  });

  $body.find('#region-image-file').on('change', e => {
    $form.submit();
  });

  $.getJSON('/admin/index.php?r=test/pass-test/images', {testing_id: testingId})
    .done(response => {
      $('#image-list').empty();
      (response.images || []).forEach(image => {
        $('<div/>', {css: {cursor: 'pointer'}})
          .append(
            $('<img/>', {src: image.url, css: {width: '100%'}})
          )
          .on('click', e => {
            fragment.region.image = image;
            $body.empty().append(createRegionEditor(fragment));
          })
          .appendTo('#image-list');
      });
    })

  return $body;
}

function createRegionEditor(fragment) {

  const {url, width, height} = fragment.region.image;
  const showCorrectText = fragment['show_correct_text'] && fragment['show_correct_text'] === true;

  const content = `
  <div class="row">
      <div class="col-md-5">
          <div class="btn-group" id="select-shapes" data-toggle="buttons">
              <label class="btn btn-default active">
                  <input type="radio" name="shape" value="move" autocomplete="off" checked>
                  <i class="glyphicon glyphicon-move"></i>
              </label>
              <label class="btn btn-default">
                  <input type="radio" name="shape" value="rect" autocomplete="off"> Прямоугольник
              </label>
              <label class="btn btn-default">
                  <input type="radio" name="shape" value="circle" autocomplete="off"> Круг
              </label>
              <label class="btn btn-default">
                  <input type="radio" name="shape" value="polyline" autocomplete="off"> Линия
              </label>
          </div>
      </div>
      <div class="col-md-7">
        <label><input type="checkbox" id="show_correct_text" ${showCorrectText ? "checked" : ""}> Показывать правильный ответ для фрагмента</label>
      </div>
  </div>
  <div class="image-container-wrapper">
      <div id="image-container" style="max-height: 500px"></div>
  </div>
  <div style="display: flex; flex-direction: row; justify-content: center; padding: 10px 0; gap: 10px">
    <button type="button" class="btn btn-primary" id="save-regions">Сохранить</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
  </div>
  `;

  const $content = $(content);

  const selectShapes = $content.find('#select-shapes');

  function ShapeType(type) {
    this.type = type;
  }

  ShapeType.prototype = {
    'getType': function() {
      return this.type;
    },
    'setType': function(type) {
      this.type = type;
    },
    'isPolyline': function() {
      return this.type === 'polyline';
    }
  }

  const shapeType = new ShapeType();

  const regionSVG = new RegionsSVG($content.find('#image-container')[0]);

  regionSVG.loadImage(url, width, height, fragment.region.regions || [], (args) => {

    let initialZoom = 0.5;
    if (height > 500) {
      initialZoom = 500 / height;
    } else {
      initialZoom = height / 500;
    }

    const zoom = Panzoom($content.find('#image-container #regionImageWrap')[0], {
      //excludeClass: 'scheme-mark',
      bounds: true,
      startScale: initialZoom,
      startX: 0,
      startY: 0
    });

    $content.find('#image-container #regionImageWrap')[0].parentElement.addEventListener('wheel', zoom.zoomWithWheel);

    regionSVG.setDraggableMode();

    selectShapes.on('change', function() {
      const val = $(this).find("input[name='shape']:checked").val();
      if (val === 'move') {

        zoom.bind();
        $content.find('#image-container #regionImageWrap').css('cursor', 'move');

        regionSVG.setDraggableMode();
      } else {

        zoom.destroy();
        $content.find('#image-container #regionImageWrap').css('cursor', 'default');

        regionSVG.setDraggableMode(false);
        switch (val) {
          case 'polyline':
            regionSVG.drawPolyline();
            break;
          case 'circle':
            regionSVG.drawCircle();
            break;
          case 'rect':
            regionSVG.drawRect();
            break;
        }
      }
    });
  });

  $content.find('#save-regions').on('click', () => {
    fragment.region.regions = regionSVG.getRegions();
    fragment.show_correct_text = $content.find("#show_correct_text").is(":checked");
    modal.hide();
  });

  return $content;
}
