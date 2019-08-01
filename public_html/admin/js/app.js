
var doneCallback = function(data) {
    if (data) {
        if (data.success) {
            toastr.success("Файл успешно импортирован", "Импорт из PowerPoint");
        }
        else {
            toastr.warning("Произошла ошибка при импорте файла", "Импорт из PowerPoint");
        }
    }
    else {
        toastr.warning("Неизвестная ошибка", "Импорт из PowerPoint");
    }
};

var failCallback = function() {
    toastr.warning("Произошла ошибка при импорте файла", "Импорт из PowerPoint");
}

function storyOnBeforeSubmit(e) {
	var form = $(this),
        button = $("button[type=submit]", form);
    button.button("loading");
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: doneCallback,
        error: failCallback
    }).always(function() {
        button.button("reset");
    });
}

var StoryAudio = function() {
    "use strict";

    function deleteFile(fileName) {

    }

    return {

    };
}();
