<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $neoLabelIDField string */
/** @var $neoEntityIDField string */
/** @var $neoEntityNameField string */

$resetButton = '';
if (!empty($neoEntityIDField)) {
    $resetButton = Html::button('Очистить значение', ['class' => 'btn btn-danger', 'id' => 'reset-entity']);
}
Modal::begin([
    'id' => 'select-neo-entity-modal',
    'header' => '<h2>Выбрать сущность из Neo4j</h2>',
    'toggleButton' => ['label' => 'Выбрать сущность', 'class' => 'btn btn-default', 'style' => 'margin: 10px 0'],
    'footer' => $resetButton . Html::button('Выбрать', ['class' => 'btn btn-primary', 'id' => 'select-entity']),
]);
?>
<div style="margin: 10px 0">
<?= Html::dropDownList('neoLabels',
    null,
    [],
    ['prompt' => 'Выберите метку', 'onchange' => '', 'class' => 'form-control', 'id' => 'label-list']) ?>
</div>
<?= Html::dropDownList('neoEntities',
    null,
    [],
    ['prompt' => 'Выберите сущность', 'onchange' => '', 'class' => 'form-control', 'id' => 'entity-list']) ?>
<?php Modal::end() ?>

<?php
$js = <<< JS

(function() {
    
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
    
    var entityList = $('#entity-list'),
        labelList = $('#label-list'),
        labelIDField = $('#$neoLabelIDField'),
        entityIDField = $('#$neoEntityIDField'),
        entityNameField = $('#$neoEntityNameField');

    $("#select-neo-entity-modal").on('show.bs.modal', function() {
        resetSelect(labelList, 'Выберите метку');
        resetSelect(entityList, 'Выберите сущность');
        Neo.getLabels().done(function(data) {
            fillSelect(data, labelList, labelIDField.val());
        });
        Neo.getEntities(labelIDField.val()).done(function(data) {
            fillSelect(data, entityList, entityIDField.val());
        });
    });
    
    labelList.on('change', function() {
        var labelID = $(this).val();
        resetSelect(entityList, 'Выберите сущность');
        Neo.getEntities(labelID).done(function(data) {
            fillSelect(data, entityList);
        });
    });
    
    $("#select-entity", "#select-neo-entity-modal").on("click", function() {
        var labelID = labelList.val(),
            entityID = entityList.val(),
            entityName = entityList.find(':selected').text();
        if (!entityID || !entityName) {
            return;
        }
        labelIDField.val(labelID);
        entityIDField.val(entityID);
        entityNameField.val(entityName);
        $("#select-neo-entity-modal").modal('hide');
    });
    
    $('#reset-entity', '#select-neo-entity-modal').on('click', function() {
        labelIDField.val('');
        entityIDField.val('');
        entityNameField.val('');
        $("#select-neo-entity-modal").modal('hide');
    })
})();
JS;
$this->registerJs($js);
?>