<?php

declare(strict_types=1);

use common\components\StoryCover;
use common\models\Story;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Story $model
 */
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
                <?php if (($history = $model->userStoryHistories) !== null && $history->percent > 0): ?>
                    <div class="story-progress-wrapper">
                        <div class="story-progress" style="width: <?= $history->percent ?>%;"></div>
                    </div>
                <?php endif ?>
            </div>
            <div class="story-item-caption">
                <p class="flex-text"></p>
                <p><h3 class="story-item-name"><?= Html::encode($model->title) ?></h3></p>
            </div>
        </a>
    </div>
</div>
