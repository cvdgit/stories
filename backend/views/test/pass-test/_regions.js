
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
      <form id="region-image-form" action="/admin/index.php?r=test/pass-test/region-image-upload" method="post" enctype="multipart/form-data">
        <input type="file" id="region-image-file" name="image">
        <input type="hidden" name="fragment_id" value="${fragment.id}">
        <input type="hidden" name="testing_id" value="${testingId}">
      </form>
    </div>
  `;

  const $body = $(body);
  const $form = $body.find('#region-image-form');

  $form.on('submit', e => {
    e.preventDefault();

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
        fragment.region.image = response.data;
        $body.empty().append(createRegionEditor({region: {image: response.data}}));
      });
  });

  $body.find('#region-image-file').on('change', e => {
    $form.submit();
  });

  return $body;
}

function createRegionEditor(fragment) {

  const {url, width, height} = fragment.region.image;

  const content = `
  <div class="row">
      <div class="col-md-5">
          <div class="btn-group" id="select-shapes" data-toggle="buttons" style="margin-bottom: 20px">
              <label class="btn btn-default active">
                  <input type="radio" name="shape" value="rect" autocomplete="off" checked> Прямоугольник
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
          <div class="alert alert-info" role="alert" style="font-size:1.5rem;margin-bottom:2px">Для удаления области, выделите ее и нажмите DEL</div>
      </div>
  </div>
  <div class="image-container-wrapper">
      <div id="image-container"></div>
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

  const shapeType = new ShapeType('rect');
  selectShapes.on('change', function() {
    shapeType.setType($(this).find("input[name='shape']:checked").val());
  });

  const regionSVG = new RegionsSVG(
    $content.find('#image-container')[0],
    {path: url, width, height},
    shapeType,
    fragment.region.regions || []
  );

  $content.find('#save-regions').on('click', () => {
    fragment.region.regions = regionSVG.getRegions();
    modal.hide();
  });

  return $content;
}
