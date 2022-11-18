
$(function() {

  $('[data-toggle="tooltip"]').tooltip({container: 'body'});

	$('.modal').on('show.bs.modal', function () {
	    $('.modal').not($(this)).each(function () {
	        $(this).modal('hide');
	    });
	});

  $.ajaxSetup({
    cache: true
  });
});

var App = (function() {
	'use strict';

	return {
		getConfig: function() {
			return WikidsConfig || {};
		},
		getConfigUser: function() {
			var config = this.getConfig();
			return config.user || {};
		},
		userIsGuest: function() {
			return this.getConfigUser().isGuest;
		},
		userIsModerator: function() {
			return this.getConfigUser().isModerator;
		}
	};
})();

function onBeforeSubmitForm(formElement, callback) {
  formElement.on('beforeSubmit', function(e) {
    e.preventDefault();
    callback(formElement[0]);
    return false;
  })
    .on('submit', function(e) {
      e.preventDefault();
    });
}

function sendForm(formData, url, type) {
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
            toastr.error(xhr.responseJSON.message);
          }
        }).done(function(data) {
          if (data && data.success) {
            toastr.success(data.message || 'Успешно');
            $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
          }
          else {
            toastr.error(data['message'] || 'Неизвестная ошибка');
          }
        });
      }
    });
  }
  handler();
}

pjaxGridDeleteInit();

$(document)
  .off('pjax:success', pjaxGridDeleteInit)
  .on('pjax:success', pjaxGridDeleteInit);
