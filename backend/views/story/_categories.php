<?php

use common\models\Category;
use yii\bootstrap\Modal;
use yii\widgets\Menu;

/** @var $this yii\web\View */
/** @var $selectInputName string */
/** @var $selectInputID string */

Modal::begin([
    'id' => 'select-categories-modal',
    'header' => '<h2>Категории</h2>',
    'toggleButton' => ['label' => 'Выбрать категории', 'class' => 'btn'],
]);
?>

<div id="category-list">
<?= Menu::widget([
    'items' => Category::categoryArray(),
    'encodeLabels' => false,
    'linkTemplate' => '<label><input type="checkbox" value="{url}"> {label}</label>',
]) ?>
</div>

<div class="clearfix">
    <div class="pull-right">
        <button type="button" class="btn btn-success" id="save-categories">Сохранить</button>
    </div>
</div>

<?php Modal::end() ?>

<?php
$js = <<< JS
$('#select-categories-modal').on('show.bs.modal', function() {
    var list = $('#category-list'),
        id = '$selectInputID';
    $('input[type=checkbox]', list).prop('checked', false);
    var value = $('#' + id).val();
    if (value) {
        value.split(',').forEach(function(value) {
            $('input[value=' + value + ']', list).prop('checked', true);
        });
    }
});
$('#save-categories').on('click', function() {
    var list = $('#selected-category-list'),
        id = '$selectInputID',
        ids = [];
    list.empty();
    $('#category-list input[type=checkbox]').each(function() {
        var el = $(this);
        if (el.is(':checked')) {
            $('<span>')
              .addClass('label label-default')
              .text($.trim(el.parent().text()))
              .appendTo(list);
            list.append(' ');
            ids.push(el.val());
        }
    });
    $('#' + id).val(ids.join(',')).blur();
    $('#select-categories-modal').modal('hide');
});
JS;
$this->registerJs($js);
?>