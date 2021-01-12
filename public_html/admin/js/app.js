
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