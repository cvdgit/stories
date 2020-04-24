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
                <?php if ((int)$model['audio'] === 1): ?>
                    <span data-toggle="tooltip" title="В истории доступна озвучка" class="label label-danger story-label"><i class="glyphicon glyphicon-volume-up"></i></span>
                <?php endif ?>
                <div class="story-item-image-overlay">
                    <span></span>
                </div>
                <?php $img = empty($model['cover']) ? '/img/story-1.jpg' : StoryCover::getListThumbPath($model['cover']); ?>
                <?= Html::img($img) ?>
                <?php if ($model['history_percent'] > 0): ?>
                    <div class="story-progress-wrapper">
                        <div class="story-progress" style="width: <?= $model['history_percent'] ?>%;"></div>
                    </div>
                <?php endif ?>
            </div>
            <div class="story-item-caption">
                <p class="flex-text"></p>
                <p><h3 class="story-item-name"><?= Html::encode($model['title']) ?></h3></p>
            </div>
        </a>
    </div>
</div>