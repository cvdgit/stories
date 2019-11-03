<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\StoryCover;

/* @var $model common\models\Story */
?>
<div class="col-lg-3 col-md-4 col-sm-6">
    <div class="story-item">
        <a href="<?= Url::toRoute(['/story/view', 'alias' => $model->alias]) ?>">
            <div class="story-item-image">
                <div class="story-item-image-overlay">
                    <span></span>
                </div>
                <?php $img = empty($model->cover) ? '/img/story-1.jpg' : StoryCover::getListThumbPath($model->cover); ?>
                <?= Html::img($img) ?>
            </div>
            <div class="story-item-caption" style="flex: 0 0 auto">
                <p class="flex-text"></p>
                <p>
                    <h3 class="story-item-name" style="margin-top: 0"><?= Html::encode($model->title) ?></h3>
                </p>
            </div>
        </a>
    </div>
</div>