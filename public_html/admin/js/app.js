
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

var Neo = (function(jQuery) {
    "use strict";

    var $ = jQuery;

    function getEntities(labelID) {
        if (labelID) {
            labelID = '&label_id=' + labelID;
        }
        return $.getJSON("/admin/index.php?r=neo/entity-list" + labelID);
    }

    function getRelations(entityID) {
        return $.getJSON("/admin/index.php?r=neo/relations-list&entity_id=" + entityID);
    }

    function getRelatedEntities(entityID, relationID) {
        return $.getJSON("/admin/index.php?r=neo/related-entities-list&entity_id=" + entityID + "&relation_id=" + relationID);
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

    return {
        "getEntities": getEntities,
        "getRelations": getRelations,
        "getRelatedEntities": getRelatedEntities,
        "saveRelations": saveRelations,
        "deleteRelation": deleteRelation,
        "getLabels": getLabels
    };
})(jQuery);