<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $neoEntityIDField string */
/** @var $neoEntityNameField string */

Modal::begin([
    'id' => 'select-neo-entity-modal',
    'header' => '<h2>Выбрать сущность из Neo4j</h2>',
    'toggleButton' => ['label' => 'Выбрать', 'class' => 'btn btn-default'],
    'footer' => Html::button('Выбрать', ['class' => 'btn btn-primary', 'id' => 'select-entity']),
]);
?>
<?= Html::dropDownList('neoEntities',
    null,
    [],
    ['prompt' => 'Выбрать сущность', 'onchange' => '', 'class' => 'form-control', 'id' => 'entity-list']) ?>
<?php Modal::end() ?>

<?php
$js = <<< JS
var entityList = $('#entity-list'),
    entityIDField = $('#$neoEntityIDField'),
    entityNameField = $('#$neoEntityNameField');

$("#select-neo-entity-modal")
    .on('show.bs.modal', function() {
        entityList.empty();
        Neo.getEntities().done(function(data) {
            data.forEach(function(entity) {
                $('<option/>')
                    .text(entity.name)
                    .val(entity.id)
                    .attr('selected', (parseInt(entity.id) === parseInt(entityIDField.val())))
                    .appendTo(entityList);
            });
        })
    });
$("#select-entity", "#select-neo-entity-modal").on("click", function() {
    var entityID = entityList.val(),
        entityName = entityList.find(':selected').text();
    if (!entityID || !entityName) {
        return;
    }
    entityIDField.val(entityID);
    entityNameField.val(entityName);
    $("#select-neo-entity-modal").modal('hide');
})
JS;
$this->registerJs($js);
?>