
function BootstrapAlert() {
    this.htmlBegin = '<div class="alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    this.htmlEnd = '</div>';
}
BootstrapAlert.prototype.success = function(message) {
    return $(this.htmlBegin + message + this.htmlEnd).addClass('alert-success');
}
BootstrapAlert.prototype.error = function(message) {
    return $(this.htmlBegin + message + this.htmlEnd).addClass('alert-danger');
}

var StoryAlert = (function() {

    var $elem = $('<div class="alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>');
    var $placeholder = $('#alert_placeholder');

    function show($el) {
        return $el.appendTo($placeholder);
    }

    function success(message) {
        show($elem.text(message).addClass('alert-success'));
    }

    function error(message) {
        show($elem.text(message).addClass('alert-danger'));
    }

    return {
        success: success,
        error: error
    };
})();

window.storyAlert = new BootstrapAlert();

var doneCallback = function(data) {
    if (data) {
        var elem;
        if (data.success) {
            elem = storyAlert.success('Операция выполнена успешно');
        }
        else {
            elem = storyAlert.error('Произошла ошибка при выполнении операции');
        }
        elem.appendTo('#alert_placeholder');
    }
};

var failCallback = function(data) {
    $('#alert_placeholder').append(storyAlert.error(data.responseText));
}

function storyOnBeforeSubmit(e) {
	var form = $(this),
        button = $('button[type=submit]', form);
    button.button('loading');
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: doneCallback,
        error: failCallback
    }).always(function() { button.button('reset') });
}