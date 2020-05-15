<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model backend\models\NeoSlideRelationsForm */
?>

<div class="modal fade" id="neo-relation-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Связи</h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['neo/create-relation'],
                    'id' => 'create-relation-form',
                    'validateOnSubmit' => false,
                ]); ?>
                <?= $form->field($model, 'entity_id')->dropDownList([]) ?>
                <?= $form->field($model, 'entity_name')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'relation_id')->dropDownList([], ['disabled' => true]) ?>
                <?= $form->field($model, 'relation_name')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'related_entity_id')->dropDownList([], ['disabled' => true]) ?>
                <?= $form->field($model, 'related_entity_name')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
                <div class="form-group">
                    <?= Html::submitButton('Добавить новую связь', ['class' => 'btn btn-primary']) ?>
                </div>
                <?php ActiveForm::end(); ?>
                <h4 class="text-center">Существующие связи</h4>
                <table class="table table-bordered" id="slide-relations">
                    <thead>
                        <tr>
                            <th>Сущность</th>
                            <th>Отношение</th>
                            <th>Связанная сущность</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php

$js = <<<JS
(function() {
    
    function resetSelect(select, emptyText) {
        select.empty();
        $('<option/>').text(emptyText).val('').appendTo(select);
    }
    
    function fillSelect(data, select, selected, nameCallback, dataAttributes) {
        dataAttributes = dataAttributes || [];
        data.forEach(function(row) {
            var item = $('<option/>')
                .html(typeof nameCallback === 'function' ? nameCallback(row) : row.name)
                .val(row.id);
            if (dataAttributes.length) {
                var attr = {};
                dataAttributes.forEach(function(attrName) {
                    attr[attrName] = row[attrName];
                });
                item.data(attr);
            }
            if (selected) {
                item.attr('selected', parseInt(row.id) === parseInt(selected))
            }
            item.appendTo(select);
        });
    }
    
    var modal = $('#neo-relation-modal');

    $('select', modal).on('change', function() {
        var targetID = $(this).attr('id').replace('_id', '_name');
        $('#' + targetID).val($(this).find(':selected').text());
    });
    
    var entityList = $('#neosliderelationsform-entity_id', modal);
    entityList.on('change', function() {
        resetSelect(relationList, 'Загрузка...');
        relatedEntityList.empty().prop('disabled', true);
        var entityID = $(this).val();
        if (!entityID) {
            relationList.empty().prop('disabled', true);
            relatedEntityList.empty().prop('disabled', true);
            return;
        }
        var formatDirection = function(direction) {
            return direction === 'in' ? '&larr;' : '&rarr;';
        };
        Neo.getRelations(entityID).done(function(data) {
            resetSelect(relationList, 'Выберите связь');
            fillSelect(data, relationList, null, function(row) { return row.name + ' (' + formatDirection(row.direction) + ')'; }, ['direction']);
            relationList.prop('disabled', false);
        });
    });    
    
    var relationList = $('#neosliderelationsform-relation_id', modal);
    relationList.on('change', function() {
        resetSelect(relatedEntityList, 'Загрузка...');
        var entityID = $('#neosliderelationsform-entity_id').val();
        var relationID = $(this).val();
        if (!entityID || !relationID) {
            relatedEntityList.empty().prop('disabled', true);
            return;
        }
        var direction = $(this).find(':selected').data('direction');
        Neo.getRelatedEntities(entityID, relationID, direction).done(function(entity) {
            resetSelect(relatedEntityList, 'Выберите связанную сущность');
            fillSelect(entity, relatedEntityList);
            relatedEntityList.prop('disabled', false);
        });
    });
    
    var relatedEntityList = $('#neosliderelationsform-related_entity_id', modal);
    
    function loadRelations() {
        var tableBody = $('#slide-relations tbody', modal);
        tableBody.empty();
        tableBody.append($('<tr/>').append($('<td/>').attr('colspan', 4).text('Загрузка...')));
        $.getJSON('/admin/index.php?r=slide/slide-relations', {'slide_id': StoryEditor.getCurrentSlideID()})        
            .done(function(data) {
                tableBody.empty();
                if (data && data.length) {
                    data.forEach(function(row) {
                        var deleteRelation = $('<a/>')
                            .attr('href', '#')
                            .on('click', function(e) {
                                e.preventDefault();
                                if (!confirm('Удалить связь?')) {
                                    return;
                                }
                                var str = 'slide_id=' + StoryEditor.getCurrentSlideID() + '&entity_id=' + row.entity_id + '&relation_id=' + row.relation_id + '&related_entity_id=' + row.related_entity_id;
                                Neo.deleteRelation(str);
                                $(this).parent().parent().remove();
                            })
                            .html('<span class="glyphicon glyphicon-trash" title="Удалить" style="color: rgb(255, 0, 0); font-weight: 500;"></span>');
                        $('<tr/>')
                            .append($('<td/>').text(row.entity_name))
                            .append($('<td/>').text(row.relation_name))
                            .append($('<td/>').text(row.related_entity_name))
                            .append($('<td/>').append(deleteRelation))
                            .appendTo(tableBody);
                    });
                }
                else {
                    tableBody.append($('<tr/>').append($('<td/>').attr('colspan', 4).text('Пусто')));
                }
            });
    }
    
    modal.on('show.bs.modal', function() {
        
        $('#create-relation-form')[0].reset();
        relationList.empty().prop('disabled', true);
        relatedEntityList.empty().prop('disabled', true);
        
        $('#neosliderelationsform-slide_id', this).val(StoryEditor.getCurrentSlideID());
        
        resetSelect(entityList, 'Загрузка...');
        Neo.getEntities().done(function(data) {
            resetSelect(entityList, 'Выберите сущность');
            fillSelect(data, entityList);
        });
        
        loadRelations();
    });
    
    $('#create-relation-form').submit(function(e) {
        e.preventDefault();
        var yiiform = $(this);
        $.ajax({
            type: yiiform.attr('method'),
            url: yiiform.attr('action'),
            data: yiiform.serializeArray()
        }).done(function(data) {
            if (data) {
                if (data.success) {
                    toastr.success('Успешно');
                    loadRelations();
                }
                else {
                    toastr.error(JSON.stringify(data.errors));
                }
            }
            else {
                toastr.error('Неизвестная ошибка');
            }
        })
        .fail(function(data) {
            toastr.error(data.responseJSON.message);
        });
    });
})();
JS;
$this->registerJs($js);
