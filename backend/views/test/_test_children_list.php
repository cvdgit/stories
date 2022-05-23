<?php

use backend\helpers\StoryHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
/** @var $model common\models\StoryTest */
?>
<div>
    <p>
        <?= Html::a('Создать вариант тест', ['test-variant/create', 'parent_id' => $model->id], ['class' => 'btn btn-primary', 'id' => 'create-test-variant']) ?>
    </p>
    <h4>Варианты теста</h4>
    <table class="table table-bordered" id="test-variants-table">
        <thead>
        <tr>
            <th>Вариант</th>
            <th>Переход</th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal remote fade" id="test-variant-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="update-test-variant-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?= $this->render('_neo_questions_modal', ['testId' => $model->id]) ?>

<?php
$testVariants = Json::encode($model->getChildrenTestsAsArray());
$deleteUrl = Url::to(['test-variant/delete']);
$updateUrl = Url::to(['test-variant/update']);
$storyViewUrl = StoryHelper::createStoryViewUrl('ALIAS');
$js = <<< JS

var createVariantModal = $('#test-variant-modal'),
    updateVariantModal = $('#update-test-variant-modal');

$('#test-variants-table').on('click', '.update-test-variant', function(e) {
    e.preventDefault();
    updateVariantModal
        .modal({'remote': $(this).attr('href')});
});

updateVariantModal.on('hide.bs.modal', function() {
    $(this).find('.modal-content').html('');
    $(this).removeData('bs.modal');
});

function showModal(element, remote) {
    element.modal({'remote': remote});
}

function getUpdateVariantUrl(id) {
    return '$updateUrl' + '&id=' + id;
}

function getStoryViewUrl(alias) {
    return '$storyViewUrl'.replace('ALIAS', alias);
}

function createVariantStory(story) {
    return $('<a/>', {
        'href': getStoryViewUrl(story.alias),
        'text': story.title,
        'target': '_blank'
    });
}

var testVariants = $testVariants;
window.fillTestVariantsTable = function(params) {
    var table = $('#test-variants-table tbody');
    table.empty();
    params.forEach(function(param) {
        var updateLink = $('<a/>')
            .addClass('update-test-variant')
            .attr({href: getUpdateVariantUrl(param.id), title: 'Изменить запись'})
            .html('<i class="glyphicon glyphicon-edit"></i>')
            .css('marginRight', '10px');
        var deleteLink = $('<a/>')
            .attr({href: '#', title: 'Удалить запись'})
            .html('<i class="glyphicon glyphicon-trash"></i>')
            .on('click', function(e) {
                e.preventDefault();
                if (!confirm('Удалить запись?')) {
                    return false;
                }
                var that = this;
                $.getJSON('$deleteUrl', {id: param.id})
                .done(function(response) {
                    if (response && response.success) {
                        $(that).parent().parent().remove();
                    }
                })
            });

        var tr = $('<tr/>');
        tr.append($('<td/>').text(param.title));

        var td = $('<td/>');
        if (param.stories.length) {
            param.stories.forEach(function(story) {
                td.append(createVariantStory(story));
            });
        }
        tr.append(td);

        tr.append($('<td/>')
            .append(updateLink)
            .append(deleteLink)
        );
        tr.appendTo(table);
    });
}
fillTestVariantsTable(testVariants);

$('#create-test-variant').on('click', function(e) {
    e.preventDefault();
    createVariantModal.modal({'remote': $(this).attr('href')});
});

window.loadNeoTaxon = function(element) {
    element.empty().append($('<option/>').val('').text('Выберите значение'));
    var def = $.Deferred();
    Neo.getTaxonList()
        .done(function(response) {
            response.forEach(function(item) {
                $('<option/>')
                    .text(item.name)
                    .val(item.label)
                    .prop('selected', (element.attr('data-value') === item.label))
                    .appendTo(element);
            });
            def.resolve();
        });
    return def.promise();
};

window.loadNeoTaxonValues = function(taxon, element) {
    element.empty().append($('<option/>').val('').text('Выберите значение'));
    Neo.getTaxonValueList(taxon)
        .done(function(response) {
            response.forEach(function(item) {
                $('<option/>')
                    .text(item.name)
                    .val(item.name)
                    .prop('selected', (element.attr('data-value') === item.name))
                    .appendTo(element);
            });
        });
};

window.loadNeoQuestionValues = function(list, id, selected) {

    selected = selected || [];
    function isSelected(name, value) {
        return selected.filter(function(item) {
            return item.name == name && item.value == value;
        }).length > 0;
    }

    function addChangeEvent(values, targetParam, relatedParam, valueKey) {

        $('select[name=' + targetParam + ']', list).on('change', function() {

            var relatedSelect = $('select[name=' + relatedParam + ']', list),
                currentValue = $(this).find('option:selected').attr('data-id');

            relatedSelect.empty();
            $('<option/>')
                .text('Выберите значение')
                .val('')
                .addClass('empty-value')
                .appendTo(relatedSelect);

            if (currentValue === '') {
                return;
            }

            var selectValues = [];
            values.forEach(function(item) {
                if (item.param == relatedParam) {
                    selectValues = item.values;
                    return;
                }
            });

            selectValues
                .filter(function(value) {
                    return currentValue == value.entity_id;
                })
                .forEach(function(item) {
                    $('<option/>')
                        .text(item.name)
                        .attr('data-id', item.id)
                        .val(item[valueKey])
                        .prop('selected', isSelected(relatedParam, item[valueKey]))
                        .appendTo(relatedSelect);
                });
        })
        .trigger('change');
    }

    Neo.getQuestionValues(id).done(function(response) {

        var values = response.data;
        var form = $('.test-variant-form');

        response.data.forEach(function(field) {

            var select = $('<select/>')
                .attr('name', field.param)
                .addClass('form-control')
                .addClass('test-variant-config-element');

            $('<option/>')
                .text('Выберите значение')
                .val('')
                .addClass('empty-value')
                .appendTo(select);

            $('<div/>').addClass('form-group')
                .append($('<label/>').addClass('control-label').text(field.title))
                .append(select)
                .appendTo(list);

            if (field.relation.length) {
                addChangeEvent(values, field.relation, field.param, field.field);
            }
            else {
                field.values.forEach(function(item) {
                    $('<option/>')
                        .text(item.name)
                        .val(item[field.field])
                        .attr('data-id', item.id)
                        .prop('selected', isSelected(field.param, item[field.field]))
                        .appendTo(select);
                });
            }
        });
    });
};

window.fillTestVariantConfig = function(form, id) {
    form = $(form);
    var values = [];
    form.find('.test-variant-config-element').each(function(i, elem) {
        var val = $(this).val() || '';
        if (val.length) {
            values.push($(this).attr('name') + '=' + val);
        }
    });
    form.find('#' + id).val(values.join(';'));
};

createVariantModal
    .on('shown.bs.modal', function() {
        var id = $('#createform-neo_question_id').val();
        var list = $('.question-config', this);
        if (!list.find('div.form-group').length) {
            list.empty();
            window.loadNeoQuestionValues(list, id);
        }
    });

function createVariantParams(params) {
    return params.split(';').map(function(value) {
        return {"name": value.split('=')[0], "value": value.split('=')[1]};
    });
}

updateVariantModal
    .on('loaded.bs.modal', function() {

        var id = $('#updateform-neo_question_id').val();
        var params = $('#updateform-question_params').val();
        var list = $('.question-config', this);
        list.empty();

        window.loadNeoQuestionValues(list, id, createVariantParams(params));

        var wrongElementList = $(this).find('.wrong-answer-list > .wrong-answer-list-item');
        wrongElementList.each(function() {
            var elem = $(this).find('.taxon-name-select select'),
                valueElem = $(this).find('.taxon-value-select select');
            window
                .loadNeoTaxon(elem)
                .done(function() {
                    window.loadNeoTaxonValues(elem.attr('data-value'), valueElem);
                });
        });
    });

function createWrongAnswerRow(list) {
    var nextIndex = $('.wrong-answer-list-item', list).length;
    var newItem = $('.wrong-answer-list-item:eq(0)', list).clone();
    newItem
        .find("label")
            .attr("for", function(index, currentValue) {
                return currentValue.replace("0", nextIndex)
            }).end()
        .find("select")
            .val('')
            .attr('data-value', '')
            .attr("id", function(index, currentValue) {
                return currentValue.replace("0", nextIndex)
            })
            .attr("name", function(index, currentValue) {
                return currentValue.replace("0", nextIndex)
            }).end()
        .find('a.delete-wrong-answer-row')
            .attr('href', '#')
            .attr('data-index', nextIndex)
            .end();
    $(list).append(newItem);
}

$('.test-sidebar').on('click', '.wrong-answer-add-item', function(e) {
    var list = $(this).parent().parent().find('.wrong-answer-list');
    if (list.find('.wrong-answer-list-item:visible').length) {
        createWrongAnswerRow(list);
    }
    else {
        list.find('.wrong-answer-list-item').removeClass('hide');
        fillWrongAnswerData(list.find('.wrong-answer-list-item'));
    }
});

function fillWrongAnswerData(item) {
    var taxonNameSelect = $('.taxon-name-select select', item);
    taxonNameSelect.empty().append($('<option/>').val('').text('Выберите значение'));
    window.loadNeoTaxon(taxonNameSelect);
}

$('.test-sidebar').on('click', '.delete-wrong-answer-row', function(e) {
    e.preventDefault();
    if (!confirm('Удалить строку?')) {
        return;
    }
    var index = parseInt($(this).attr('data-index')),
    element = $(this);
    function removeBlock() {
        var block = element.parent().parent();
        if (index === 0) {
            block.find('select').val('');
            block.addClass('hide');
        }
        else {
            block.remove();
        }
    }
    removeBlock();
});

$('.test-sidebar').on('change', '.taxon-name-select select', function(e) {
    window.loadNeoTaxonValues(this.value, $(this).parent().parent().parent().find('.taxon-value-select select'));
});

var test_id = parseInt(location.hash.slice(1));
if (Number.isInteger(test_id)) {
    setTimeout(function() {
        showModal(updateVariantModal, getUpdateVariantUrl(test_id));
    }, 500);
}

JS;
$this->registerJs($js);
