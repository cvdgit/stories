<?php
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

<?php
$testVariants = Json::encode($model->getChildrenTestsAsArray());
$deleteUrl = Url::to(['test-variant/delete']);
$updateUrl = Url::to(['test-variant/update']);
$js = <<< JS

var createVariantModal = $('#test-variant-modal'),
    updateVariantModal = $('#update-test-variant-modal');

$('#test-variants-table').on('click', '.update-test-variant', function(e) {
    e.preventDefault();
    updateVariantModal
        .modal({'remote': $(this).attr('href')});
});

updateVariantModal.on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
});

var testVariants = $testVariants;
window.fillTestVariantsTable = function(params) {
    var table = $('#test-variants-table tbody');
    table.empty();
    params.forEach(function(param) {
        var updateLink = $('<a/>')
            .addClass('update-test-variant')
            .attr({href: '$updateUrl' + '&id=' + param.id, title: 'Изменить запись'})
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
        $('<tr/>')
            .append($('<td/>').text(param.title))
            .append($('<td/>').append(updateLink).append(deleteLink))
            .appendTo(table);
    });
}
fillTestVariantsTable(testVariants);

$('#create-test-variant').on('click', function(e) {
    e.preventDefault();
    createVariantModal.modal({'remote': $(this).attr('href')});
});

window.loadNeoTaxon = function(element) {
    console.log(element);
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

createVariantModal
    .on('shown.bs.modal', function() {
        var taxonNameElement = $('#createform-taxonname', this);
        var taxonValueElement = $('#createform-taxonvalue', this);
        window.loadNeoTaxon(taxonNameElement);
        taxonNameElement
            .off('change')
            .on('change', function() {
                window.loadNeoTaxonValues(taxonNameElement.val(), taxonValueElement);
            });
    });

updateVariantModal
    .on('shown.bs.modal', function() {
        
        var taxonNameElement = $('#updateform-taxonname', updateVariantModal);
        var taxonValueElement = $('#updateform-taxonvalue', updateVariantModal);
    
        var wrongElementList = $('.wrong-answer-list > .wrong-answer-list-item:visible', updateVariantModal);
        
        window
            .loadNeoTaxon(taxonNameElement)
            .done(function() {
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
        
        taxonNameElement
            .off('change')
            .on('change', function() {
                window.loadNeoTaxonValues(taxonNameElement.attr('data-value'), taxonValueElement);
            })
            .trigger('change');
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

JS;
$this->registerJs($js);