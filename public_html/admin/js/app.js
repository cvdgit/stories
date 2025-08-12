

function pjaxGridDeleteInit() {
  var handler = function() {
    $('.pjax-delete-link').on('click', function(e) {
      e.preventDefault();
      var deleteUrl = $(this).attr('delete-url');
      var pjaxContainer = $(this).attr('pjax-container');
      var result = confirm('Подтверждаете удаление записи?');
      if (result) {
        $.ajax({
          url: deleteUrl,
          type: 'post',
          error: function(xhr, status, error) {
            alert(xhr.responseJSON.message)
            //toastr.error(xhr.responseJSON.message);
          }
        }).done(function(data) {
          if (data && data.success) {
            //toastr.success('Успешно');
            $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
          }
          else {
            //toastr.error(data['message'] || 'Неизвестная ошибка');
            alert(data['message'] || 'Неизвестная ошибка');
          }
        });
      }
    });
  }
  handler();
  /*  $(document)
      .off('ready pjax:success', handler)
      .on('ready pjax:success', handler);*/
}

pjaxGridDeleteInit();

$(document)
  .off('pjax:success', pjaxGridDeleteInit)
  .on('pjax:success', pjaxGridDeleteInit);

(function() {
  $.ajaxSetup({
    cache: true
  });
})();

function queryStringToJSON(qs) {
    var pairs = qs.split('&');
    var result = {};
    pairs.forEach(function(p) {

        var pair = p.split('=');

        var key = pair[0];
        key = key.replace(/\+/g, '%20');
        key = decodeURIComponent(key);

        var key_is_array = false;
        if (key.indexOf('[]') !== -1) {
            key = key.slice(0, key.indexOf('[]'));
            key_is_array = true;
        }

        var value = pair[1] || '';

        value = value.replace(/\+/g, '%20');
        value = decodeURIComponent(value);

        if (value.indexOf('&') !== -1) {
            if (!result[key] && key_is_array) {
                value = [queryStringToJSON(value)];
            }
            else {
                value = queryStringToJSON(value);
            }
        }

        if (result[key]) {
            if (Object.prototype.toString.call(result[key]) === '[object Array]') {
                result[key].push(value);
            } else {
                result[key] = [result[key], value];
            }
        } else {
            result[key] = value;
        }

    });
    return JSON.parse(JSON.stringify(result));
}

var Neo = (function(jQuery) {
    "use strict";

    var $ = jQuery;
    var cache = {};

    function getEntities(labelID) {
        labelID = labelID || '';
        if (labelID) {
            labelID = '&label_id=' + labelID;
        }
        return $.getJSON("/admin/index.php?r=neo/entity-list" + labelID);
    }

    function getRelations(entityID) {
        return $.getJSON("/admin/index.php?r=neo/relations-list&entity_id=" + entityID);
    }

    function getRelatedEntities(entityID, relationID, direction) {
        return $.getJSON('/admin/index.php?r=neo/related-entities-list', {'entity_id': entityID, 'relation_id': relationID, 'direction': direction});
    }

    function saveRelations(relations) {
        return $.post('/admin/index.php?r=neo/save-relations', relations);
    }

    function deleteRelation(relation) {
        return $.post('/admin/index.php?r=neo/delete-relation', relation);
    }

    function getLabels() {
        return $.post('/admin/index.php?r=neo/labels');
    }

    function getQuestions(param, paramValue) {
        return $.getJSON('/admin/index.php?r=neo/questions', {"param": param, "value": paramValue});
    }

    function getQuestionList() {
        return $.getJSON('/admin/index.php?r=neo/question-list');
    }

    function questions(questionID, params) {
        params = params || '';
        var data = {'id': questionID};
        if (params.length) {
            data = $.extend(data, queryStringToJSON(params));
        }
        return $.getJSON('/admin/index.php?r=neo/question-get', data);
    }

    function getTaxonList() {
        var key = 'taxonNames';
        if (cache[key]) {
            var def = $.Deferred();
            def.resolve(cache[key]);
            return def.promise();
        }
        return $.getJSON('/admin/index.php?r=neo/taxon-list')
            .done(function(response) {
                cache[key] = response;
            });
    }

    function getTaxonValueList(taxon) {
        var key = taxon;
        if (cache[key]) {
            var def = $.Deferred();
            def.resolve(cache[key]);
            return def.promise();
        }
        return $.getJSON('/admin/index.php?r=neo/taxon-value-list', {"taxon": taxon})
            .done(function(response) {
                cache[key] = response;
            });
    }

    function getQuestionValues(id) {
        return $.getJSON('/admin/index.php?r=neo/question-values', {"id": id});
    }

    return {
        "getEntities": getEntities,
        "getRelations": getRelations,
        "getRelatedEntities": getRelatedEntities,
        "saveRelations": saveRelations,
        "deleteRelation": deleteRelation,
        "getLabels": getLabels,
        "getQuestions": getQuestions,
        "getQuestionList": getQuestionList,
        "questions": questions,
        "getTaxonList": getTaxonList,
        "getTaxonValueList": getTaxonValueList,
        "getQuestionValues": getQuestionValues
    };
})(jQuery);

function yiiModalFormInit(formElement, doneCallback, failCallback, alwaysCallback) {
    formElement.on('beforeSubmit', function(e) {
        e.preventDefault();
        var $btn = $(this).find('button[type=submit]');
        $btn.button('loading');
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
            .done(doneCallback)
            .fail(failCallback)
            .always(function() {
                $btn.button('reset');
                if (typeof alwaysCallback === 'function') {
                    alwaysCallback();
                }
            });
        return false;
    })
        .on('submit', function(e) {
            e.preventDefault();
        });
}

function RemoteModal({id, title, dialogClassName}) {

  const content = `
    <div class="modal fade" tabindex="-1" id="${id}">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="display: flex; justify-content: space-between">
            <h5 class="modal-title" style="margin-right: auto">${title}</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">...</div>
        </div>
      </div>
    </div>
    `;

  if ($('body').find(`div#${id}`).length) {
    $('body').find(`div#${id}`).remove();
  }

  $('body').append(content);

  const element = $('body').find(`div#${id}`);

  if (dialogClassName) {
    element.find('.modal-dialog').addClass(dialogClassName);
  }

  element.on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-body').html('');
  });

  return {
    show({url, callback}) {
      element
        .off('show.bs.modal')
        .on('show.bs.modal', function() {
          $(this).find('.modal-body').load(url, callback);
        });
      element.modal();
    },
    hide() {
      element.modal('hide');
    }
  };
}

function attachBeforeSubmit(form, callback) {
  $(form)
    .on('beforeSubmit', function(e) {
      e.preventDefault();
      callback(form);
      return false;
    })
    .on('submit', function(e) {
      e.preventDefault();
    });
}

function sendForm(url, type, formData) {
  return $.ajax({
    url,
    type,
    data: formData,
    dataType: 'json',
    cache: false,
    contentType: false,
    processData: false
  });
}

const SimpleModal = function({id, title}) {

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

  const element = $('body').find(`div#${id}`);

  element
    .off('show.bs.modal')
    .on('show.bs.modal', () => {});
  //.off('hide.bs.modal');
  //.on('hide.bs.modal', hideCallback);

  this.show = ({body}) => {
    element.find('.modal-body')
      .empty()
      .append(body);
    element.modal();
  };

  this.hide = () => {
    element.modal('hide');
  }

  /**
   * @returns {*|jQuery}
   */
  this.getElement = () => element;
}

window.modalHelper = {
  btnLoading(elem) {
    $(elem).attr("data-original-text", $(elem).html());
    $(elem).prop("disabled", true);
    $(elem).html('<i class="spinner-border spinner-border-sm"></i> Loading...');
  },
  btnReset(elem) {
    $(elem).prop("disabled", false);
    $(elem).html($(elem).attr("data-original-text"));
  }
}

window.formHelper = {
  sendForm(url, type, formData) {
    return $.ajax({
      url,
      type,
      data: formData,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false
    });
  },
  attachBeforeSubmit(form, callback) {
    form
      .on('beforeSubmit', function (e) {
        e.preventDefault();
        callback(form);
        return false;
      })
      .on('submit', function (e) {
        e.preventDefault();
      });
  }
}
