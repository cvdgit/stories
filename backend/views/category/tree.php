<?php
use common\helpers\Url;
use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;
/** @var $treeItems array */
/** @var $data array */
/** @var $rootCategory common\models\Category */
$this->title = 'Категории';
$css = <<<CSS
ul.fancytree-container {
    border: 0 none;
}
.category-index .page-header {
    margin-top: 0;
    margin-bottom: 0;
    border-color: #ddd;
}
.category-index .tree-control {
    padding: 10px;
    background-color: #eee;
    margin-bottom: 10px;
}
#form-container {
    border: 1px #ddd solid;
    padding: 10px;
    min-height: 500px;
}
#form-container.form-loading {
    background-image: url("/img/loading.gif");
    background-repeat: no-repeat;
    background-position: 50% 50%;
}
CSS;
$this->registerCss($css);
?>
<div class="category-index">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="row row-no-gutters tree-control">
        <div class="col-md-1">
            Дерево:
        </div>
        <div class="col-md-4">
            <div class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-default"><?= Html::encode($rootCategory->name) ?> <b class="caret"></b></a>
                <?= \yii\bootstrap\Dropdown::widget([
                    'items' => $treeItems,
                ]) ?>
            </div>
        </div>
        <div class="col-md-7">
            <?= Html::a('Создать категорию', ['create', 'tree' => $rootCategory->tree], ['class' => 'btn btn-success']) ?>
            <a href="#create-tree-modal" data-toggle="modal" class="btn btn-link">Создать дерево</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php if (count($data) === 0): ?>
            <p>
                <?= Html::a('Создать категорию', ['create', 'tree' => $rootCategory->tree], ['class' => 'btn btn-success']) ?>
            </p>
            <?php else: ?>
            <h4>Дерево категорий</h4>
            <?= FancytreeWidget::widget([
                'options' =>[
                    'source' => $data,
                    'extensions' => ['dnd'],
                    'minExpandLevel' => 2,
                    'dnd' => [
                        'preventVoidMoves' => true,
                        'preventRecursiveMoves' => true,
                        'autoExpandMS' => 400,
                        'dragStart' => new JsExpression('function(node, data) {
				            return true;
			            }'),
                        'dragEnter' => new JsExpression('function(node, data) {
				            return true;
			            }'),
                        'dragDrop' => new JsExpression('function(node, data) {
                            $.get("' . Url::to(['category/move']) . '", {item: data.otherNode.data.url, action: data.hitMode, second: data.node.data.url}, function() {
                                data.otherNode.moveTo(node, data.hitMode);
                                toastr.success("Успешно");
                            });
			            }'),
                    ],
                    'activate' => new JsExpression('treeItemActivate'),
                ]
            ]) ?>
            <?php endif ?>
        </div>
        <div class="col-md-8">
            <div id="form-container" style="display: none"></div>
        </div>
    </div>
</div>
<?= $this->render('_tree_form') ?>
<?php
$url = Url::to(['category/update-ajax']);
$js = <<<JS
function treeItemActivate(event, data) {
    var title = data.node.title;
    var id = data.node.data.url;
    $("#form-container")
        .empty()
        .fadeIn()
        .addClass('form-loading')
    $.get('$url', {id: id}, function(data) {
        $("#form-container")
            .removeClass('form-loading')
            .html(data);
    });
}
JS;
$this->registerJs($js);
