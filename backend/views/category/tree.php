<?php

use common\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="row">
        <div class="col-md-6">
            <?= \wbraganca\fancytree\FancytreeWidget::widget([
                'options' =>[
                    'source' => $data,
                    'extensions' => ['dnd'],
                    'minExpandLevel' => 1,
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
                            });
			            }'),
                    ],
                    'activate' => new JsExpression('function(event, data) {
                        var title = data.node.title;
                        var id = data.node.data.url;
                        $.get("' . Url::to(['category/update-ajax']) . '", {id: id}, function(data) {
                            $("#form-container").html(data);
                        });
                    }'),
                ]
            ]) ?>
        </div>
        <div class="col-md-6">
            <div id="form-container"></div>
        </div>
    </div>
</div>
