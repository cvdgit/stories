<?php

use yii\helpers\Html;

/* @var $model common\models\Story */
?>

<div class="modal fade" id="neo-relation-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Связи</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-md-12">
                        <?= Html::label('Сущность:', 'neo-entity-name') ?>
                        <?= Html::textInput('', '', ['class' => 'form-control', 'id' => 'neo-entity-name', 'readonly' => true]) ?>
                        <?= Html::hiddenInput('', '', ['id' => 'neo-entity-id']) ?>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-md-12">
                        <?= Html::label('Отношение:') ?>
                        <?= Html::dropDownList('',
                            null,
                            [],
                            ['prompt' => 'Выберите связь', 'id' => 'neo-relation-list', 'class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-md-12">
                        <?= Html::label('Сущность:') ?>
                        <?= Html::dropDownList('',
                            null,
                            [],
                            ['prompt' => 'Выберите сущность', 'id' => 'neo-related-entities-list', 'class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" style="display: none" id="neo-delete-relation">Удалить связь</button>
                <button class="btn btn-primary" id="neo-save-relations">Сохранить</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<?php

$js = <<<JS
(function() {
    
    var modal = $('#neo-relation-modal'),
        relationList = $('#neo-relation-list'),
        relatedEntitiesList = $('#neo-related-entities-list'),
        entityID = $('#neo-entity-id').val(),
        deleteButton = $('#neo-delete-relation', modal);

    relationList.on('change', function() {
        resetSelect(relatedEntitiesList, 'Выберите сущность');
        var relationID = $(this).val();
        if (!relationID) {
            return;
        }
        Neo.getRelatedEntities(entityID, relationID).done(function(entity) {
            fillSelect([entity], relatedEntitiesList);
        });
    });

    function resetSelect(select, emptyText) {
        select.empty();
        $('<option/>').text(emptyText).val('').appendTo(select);
    }
    
    function fillSelect(data, select, selected) {
        data.forEach(function(row) {
            var item = $('<option/>')
                .text(row.name)
                .val(row.id);
            if (selected) {
                item.attr('selected', parseInt(row.id) === parseInt(selected))
            }
            item.appendTo(select);
        });
    }

    modal.on("show.bs.modal", function() {
        resetSelect(relationList, 'Выберите отношение');
        resetSelect(relatedEntitiesList, 'Выберите сущность');
        deleteButton.hide();
        $.getJSON('/admin/index.php?r=slide/slide-relations', {"slide_id": StoryEditor.getCurrentSlideID()})
            .done(function(data) {
                if (data) {
                    var selectedEntityID = data.entity_id,
                        relationID = data.relation_id;
                    $.when(Neo.getRelations(entityID), Neo.getRelatedEntities(entityID, relationID)).done(function(data1, data2) {
                        data1 = data1[0];
                        fillSelect(data1, relationList, relationID);
                        data2 = data2[0];
                        fillSelect([data2], relatedEntitiesList, selectedEntityID);
                    });
                    deleteButton.show();
                }
                else {
                    Neo.getRelations(entityID).done(function(data) {
                        fillSelect(data, relationList);
                    });
                }
            });
    });

    $('#neo-save-relations').on('click', function() {
        var relationID = $('#neo-relation-list').val();
        var relatedEntityID = $('#neo-related-entities-list').val();
        var str = 'slide_id=' + StoryEditor.getCurrentSlideID() + '&relation_id=' + relationID + '&entity_id=' + relatedEntityID;
        Neo.saveRelations(str);
        modal.modal('hide');
    });
    
    deleteButton.on('click', function() {
        var relationID = $('#neo-relation-list').val();
        var relatedEntityID = $('#neo-related-entities-list').val();
        var str = 'slide_id=' + StoryEditor.getCurrentSlideID() + '&relation_id=' + relationID + '&entity_id=' + relatedEntityID;
        Neo.deleteRelation(str);
        modal.modal('hide');
    });
})();
JS;
/* @var $this yii\web\View */
$this->registerJs($js);
