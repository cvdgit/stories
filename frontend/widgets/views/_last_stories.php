<?php

use yii\helpers\Html;
use common\services\StoryService;

/* @var $this yii\web\View */
/* @var $models Story[] */

?>
<div class="grid-product">
    <div class="title-head">
        <h2 class="text-black">Новые истории</h2>
    </div>
    <?php $i = 0; $len = sizeof($models) - 1; ?>
    <?php $storyService = new StoryService; ?>
    <?php foreach ($models as $model): ?>
    <?php $first = $i == 0; $last = $i == $len; $endline = !$first && $i % 4 == 0; ?>
    <?php if ($first || $endline): ?>
    <?php if ($endline): ?>
    </div>
    <?php endif; ?>
    <div class="row">
    <?php endif; ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="product">
                <div class="images">
                    <?php $img = empty($model->cover) ? 'http://via.placeholder.com/180x210' : $storyService->getCoverPath($model->cover, true); ?>
                    <?= Html::a(Html::img($img), ['view', 'alias' => $model->alias]) ?>
                    <div class="button-group">
                        <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                        <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                    </div>
                </div>
                <div class="info-product">
                    <?= Html::a($model->title, ['/story/view', 'alias' => $model->alias], ['class' => 'title']) ?>
                </div>
            </div>
        </div>
    <?php $i++; ?>
    <?php if ($last): ?>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
    <div class="text-center">
        <?= Html::a('Посмотреть все истории', ['/story/index'], ['class' => 'custom-btn text-center']) ?>
    </div>
</div>