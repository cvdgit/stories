<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\StoryCover;

/* @var $model common\models\Story */
?>
<div class="col-lg-3 col-md-4 col-sm-6">
    <div class="story-item">
        <a href="<?= Url::toRoute(['/story/view', 'alias' => $model['alias']]) ?>">
            <div class="story-item-image">
                <div class="story-item-image-overlay">
                    <span></span>
                </div>
                <?php $img = empty($model['cover']) ? '/img/story-1.jpg' : StoryCover::getListThumbPath($model['cover']); ?>
                <?= Html::img($img) ?>
            </div>
            <div class="story-item-caption">
                <p class="flex-text"></p>
                <p>
                    <h3 class="story-item-name"><?= Html::encode($model['title']) ?><?= (int)$model['audio'] === 1 ? '<i class="glyphicon glyphicon-volume-up"></i>' : '' ?></h3>
                    <?php if ((int)$model['sub_access'] === 1): ?>
                    <span class="story-item-pay">По подписке</span>
                    <?php endif ?>
                </p>
            </div>
        </a>
    </div>
</div>