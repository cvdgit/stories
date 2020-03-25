<?php

use yii\helpers\Html;

?>
<div class="modal fade" id="slide-new-question-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Вопросы из Neo4j</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= Html::label('Метка:', 'label-list') ?>
                        <?= Html::dropDownList('', null, [], ['prompt' => 'Выберите метку', 'class' => 'form-control', 'id' => 'label-list']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= Html::label('Сущнсть:', 'entity-list') ?>
                        <?= Html::dropDownList('', null, [], ['prompt' => 'Выберите сущность', 'class' => 'form-control', 'id' => 'entity-list']) ?>
                    </div>
                </div>
                <?= Html::button('Показать вопросы', ['id' => 'show-questions', 'class' => 'btn btn-success btn-sm', 'style' => 'margin: 10px 0']) ?>
                <table class="table table-bordered" id="question-list">
                    <thead>
                    <tr>
                        <th>Вопросы</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Пусто</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="create-questions">Создать слайд с вопросами</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    
    function resetSelect(select, emptyText) {
        select.empty();
        $('<option/>').text(emptyText).val('').appendTo(select);
    }
    
    function fillSelect(data, select, selected, valueKey) {
        valueKey = valueKey || 'id';
        data.forEach(function(row) {
            var item = $('<option/>')
                .text(row.name)
                .val(row[valueKey]);
            if (selected) {
                item.attr('selected', parseInt(row.id) === parseInt(selected))
            }
            item.appendTo(select);
        });
    }
    
    var modal = $("#slide-new-question-modal");
    
    var entityList = $('#entity-list'),
        labelList = $('#label-list');
    
    var labels = [];
    
    modal.on("show.bs.modal", function() {
        resetSelect(labelList, 'Выберите метку');
        resetSelect(entityList, 'Выберите сущность');
        Neo.getLabels().done(function(data) {
            labels = data;
            fillSelect(data, labelList, null, 'label');
        });
        Neo.getEntities().done(function(data) {
            fillSelect(data, entityList, null, 'name');
        });
    });
    
    function getElementByLabel(label) {
        return labels.find(function(elem) {
            return elem.label === label;
        });
    }
    
    labelList.on('change', function() {
        resetSelect(entityList, 'Выберите сущность');
        var elem = getElementByLabel($(this).val());
        Neo.getEntities(elem ? elem.id : '').done(function(data) {
            fillSelect(data, entityList, null, 'name');
        });
    });
    
    $('#show-questions', modal).on('click', function() {
        var param = labelList.val(),
            paramValue = entityList.val();
        if (!param || !paramValue) {
            return false;
        }
        Neo.getQuestions(param, paramValue).done(function(data) {
            var list = $("table#question-list tbody", modal);
            list.empty();
            data.forEach(function(elem) {
                $('<tr><td>' + elem.question + '</td></tr>').appendTo(list);
            });
        });
    });
    
    $('#create-questions', modal).on('click', function() {
        var param = labelList.val(),
            paramValue = entityList.val();
        StoryEditor.createQuestions(param, paramValue, function() {
            modal.modal('hide');
        });
    });
})();
JS;
/** @var $this yii\web\View  */
$this->registerJs($js);
