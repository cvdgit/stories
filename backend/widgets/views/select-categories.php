<?php
use common\models\Category;
use yii\helpers\Html;
use yii\widgets\Menu;
/** @var $this yii\web\View */
/** @var $selectInputID string */
/** @var $treeID */
/** @var $onSelect */
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
<div style="margin-bottom:20px">
    <button data-toggle="modal" data-target="#select-categories-modal" type="button" class="btn btn-default">Выбрать категории</button>
</div>
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
            values = $(valuesElementID).find('input[type=hidden]').map(function() {
                return $(this).val();
            }).get();
            console.log(values);
            values = values || [];
        }
        showTreeCategories($('select[name=categoryTrees]').val(), values);
    });
    
    $('#save-categories').on('click', function() {
        
        var ids = [];
        $('#category-list input[type=checkbox]').each(function() {
            var el = $(this);
            if (el.is(':checked')) {
                ids.push({'id': el.val(), 'name': $.trim(el.parent().text())});
            }
        });
        $onSelect(ids);
        
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