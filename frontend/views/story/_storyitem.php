<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

/* @var $model common\models\Story */
?>
<div class="product">
    <a href="<?= Url::toRoute(['view', 'alias' => $model->alias]) ?>">
        <div class="images text-center">
            <?php $img = empty($model->cover) ? 'http://via.placeholder.com/180x210' : $this->context->service->getCoverPath($model->cover, true); ?>
            <?= Html::img($img) ?>
            <div class="button-group">
                <span class="custom-btn pink"><i class="fa fa-play"></i></span>
                <p class="cst-stories-type">
                    <?= ($model->sub_access) ? 'Смотреть по подписке' : 'Смотреть бесплатно' ?>
                </p>
            </div>
        </div>
    </a>
    <a href="<?= Url::toRoute(['view', 'alias' => $model->alias]) ?>">
        <div class="info-product">
            <?= Html::tag('p', Html::encode($model->title), ['class' => 'title']) ?>
            <p class="cst-p-grey"><?= ($model->sub_access) ? 'Подписка' : 'Бесплатно' ?></p>
        </div>
    </a> 
</div>
