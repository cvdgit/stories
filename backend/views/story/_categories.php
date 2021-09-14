<?php
use common\models\Category;
use yii\helpers\Html;
use yii\widgets\Menu;
/** @var $this yii\web\View */
/** @var $selectInputID string */
/** @var $treeID */
$css = <<<CSS
#category-list {
    max-height: 500px;
    min-width: 500px;
    overflow-y: auto;
    margin-bottom: 20px;
}
#category-list ul {
    list-style: none;
}
#category-list > ul {
    padding-left: 0;
}
CSS;
$this->registerCss($css);
?>
<div class="modal fade" id="select-categories-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 style="margin:0" class="modal-title">Категории</h4>
            </div>
            <div class="modal-body">
                <div style="padding-bottom:10px">
                    <?= Html::dropDownList('categoryTrees', $treeID, Category::getTreeArray(), ['class' => 'form-control', 'prompt' => 'Выберите дерево']) ?>
                </div>
                <div id="all-trees" class="hide">
                    <?php foreach (Category::getTreeArray() as $treeID => $treeName): ?>
                        <?= Html::tag('div', Menu::widget(['items' => Category::categoryArray($treeID), 'encodeLabels' => false, 'linkTemplate' => '<label><input type="checkbox" value="{url}"> {label}</label>']), ['id' => 'tree' . $treeID]) ?>
                    <?php endforeach ?>
                </div>
                <div id="category-list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="save-categories">Выбрать</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    'use strict';
    
    var valuesElementID = '$selectInputID';
    var values = [];
    
    $('#select-categories-modal').on('show.bs.modal', function() {
        if (valuesElementID.length) {
            values = $('#' + valuesElementID).val() && $('#' + valuesElementID).val().split(',');
        }
        showTreeCategories($('select[name=categoryTrees]').val(), values);
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
    
    function showTreeCategories(treeID, values) {
        var list = $('#category-list');
        list.empty();
        if (!treeID) {
            return;
        }
        list
            .append($('#all-trees').find('#tree' + treeID)[0].innerHTML);
        fillSelectedValue(list, values);
    }
    
    function fillSelectedValue(list, values) {
        $('input[type=checkbox]', list).prop('checked', false);
        if (values.length === 0) {
            return;
        }
        values.forEach(function(value) {
            $('input[value=' + value + ']', list).prop('checked', true);
        });
    }
    
    $('select[name=categoryTrees]').on('change', function() {
        var treeID = $(this).val();
        showTreeCategories(treeID, values);
    });    
})();
JS;
$this->registerJs($js);