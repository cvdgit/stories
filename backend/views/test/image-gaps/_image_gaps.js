(function() {

  const modal = new RemoteModal({id: 'image-gaps-modal', title: 'Ответы'});

  const selectShapes = $('#select-shapes');

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

  const payload = {...window.imageGapsPayload};

  const regionSVG = new RegionsSVG($('#image-container')[0], {
    onDeleteHandler: (id) => {
      payload.fragments = payload.fragments.filter(f => f.id !== id);
    }
  });

  const {url, width, height} = window.imageParams;

  const regions = (payload.fragments || []).map(fragment => {
    return {
      answer_id: fragment.id,
      type: fragment.type,
      rect: fragment.rect
    };
  });

  regionSVG.loadImage(url, width, height, regions, (args) => {

    let initialZoom = 0.5;
    if (height > 500) {
      initialZoom = 500 / height;
    } else {
      initialZoom = 1;
    }

    const containerWidth = $('#image-container').width();
    if (width > containerWidth) {
      initialZoom = containerWidth / width;
    }

    const zoom = Panzoom($('#image-container #regionImageWrap')[0], {
      excludeClass: 'scheme-mark',
      bounds: true,
      startScale: initialZoom,
      initialX: 0,
      initialY: 0
    });
    $('#image-container #regionImageWrap')[0].parentElement.addEventListener('wheel', zoom.zoomWithWheel);

    regionSVG.setDraggableMode();

    selectShapes.on('change', function() {
      const val = $(this).find("input[name='shape']:checked").val();
      if (val === 'move') {

        zoom.bind();
        $('#image-container #regionImageWrap').css('cursor', 'move');

        regionSVG.setDraggableMode();
      } else {

        zoom.destroy();
        $('#image-container #regionImageWrap').css('cursor', 'default');

        regionSVG.setDraggableMode(false);
        switch (val) {
          case 'rect':
            regionSVG.drawRect({
              attrsHandler: () => {
                return {"data-answer-id": generateUUID()}
              },
              drawEndHandler: (attrs) => {
                payload.fragments.push({
                  id: attrs["data-answer-id"],
                  type: "rect",
                  rect: {top: attrs.y, left: attrs.x, width: attrs.width, height: attrs.height},
                  items: []
                })
              }
            });
            break;
        }
      }
    });
  });

  const createTableRow = ({id, title, correct}) => $(`
    <tr data-item-id="${id}">
      <td>${title}</td>
      <td>${correct ? 'Да' : 'Нет'}</td>
      <td>
        <a href="" class="remove-row">Удалить</a>
      </td>
    </tr>
  `);

  $('#image-container').on('dblclick', '.scheme-mark', function(e) {
    //e.preventDefault();

    const fragmentId = $(this).data('answerId');

    modal.show({
      url: '/admin/index.php?r=test/image-gaps/fragment',
      callback: function() {

        const fragment = payload.fragments.find(f => f.id === fragmentId);

        fragment.items.map((item, i) => {
          createTableRow({id: item.id, title: item.title, correct: item.correct})
            .appendTo($(this).find('#list tbody'));
        });

        $(this).find("#list tbody").on("click", ".remove-row", function(e) {
          e.preventDefault();
          const $row = $(this).parent().parent();
          fragment.items = fragment.items.filter(i => i.id !== $row.attr('data-item-id'));
          $row.remove();
        });

        const formElement = document.getElementById('fragment-form');
        attachBeforeSubmit(formElement, (form) => {

          const item = {
            id: generateUUID(),
            title: $(form).find('#fragmentitemform-name').val(),
            correct: $(form).find('#fragmentitemform-correct').is(':checked')
          };

          createTableRow(item)
            .appendTo($(this).find('#list tbody'));

          fragment.items = [...fragment.items, {
            order: ((fragment.items.length && Math.max(...fragment.items.map(i => i.order))) || 0) + 1,
            ...item
          }];

          form.reset();
        });
      }
    })
  });

  $('#image-gaps-form')
    .on('beforeValidate', function() {

      regionSVG.resetSelectize();
      const $content = $('#image-container').clone();
      payload.content = $content.find('#regionImageWrap').removeAttr("transform").end().html()

      $('#updateimagegapsform-payload').val(JSON.stringify(payload));
    })
    .on('beforeSubmit', function(e) {
      e.preventDefault();

      function btnLoading(elem) {
        $(elem).attr("data-original-text", $(elem).html());
        $(elem).prop("disabled", true);
        $(elem).html('<i class="spinner-border spinner-border-sm"></i> Loading...');
      }

      function btnReset(elem) {
        $(elem).prop("disabled", false);
        $(elem).html($(elem).attr("data-original-text"));
      }

      const $btn = $(this).find('button[type=submit]');
      btnLoading($btn);

      $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: new FormData(this),
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false
      })
        .done((response) => {
          if (response && response.success) {
            if (response.url) {
              location.replace(response.url);
            }
            else {
              toastr.success('Успешно');
            }
          }
          else {
            toastr.error(response['message'] || 'Неизвестная ошибка');
          }
        })
        .fail(response => {
          toastr.error(response.responseJSON.message);
        })
        .always(function () {
          btnReset($btn);
        });

      return false;
    })
    .on('submit', function(e) {
      e.preventDefault();
    });
})();
