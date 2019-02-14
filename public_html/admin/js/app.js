
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

window.storyAlert = new BootstrapAlert();

var doneCallback = function(data) {
    if (data) {
        var elem;
        if (data.error.length)
            elem = storyAlert.error(data.error);
        else
            elem = storyAlert.success(data.success);
        elem.appendTo('#alert_placeholder');
    }
};

var failCallback = function(data) {
    $('#alert_placeholder').append(storyAlert.error(data));
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