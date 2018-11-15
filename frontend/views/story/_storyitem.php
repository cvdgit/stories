<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $model common\models\Story */
?>
<div class="product">
    <div class="images text-center">
        <?php $img = empty($model->cover) ? 'http://via.placeholder.com/180x210' : $this->context->service->getCoverPath($model->cover, true); ?>
        <?= Html::a(Html::img($img), ['view', 'alias' => $model->alias]) ?>
        <div class="button-group">
            <a href="#" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
        </div>
    </div>
    <div class="info-product">
        <?= Html::a($model->title, ['view', 'alias' => $model->alias], ['class' => 'title']) ?>
    </div>
</div>
