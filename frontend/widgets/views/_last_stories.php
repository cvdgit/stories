<?php

use yii\helpers\Html;
use common\components\StoryCover;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $models common\models\Story[] */

?>
<div class="flex-row row story-list">
    <?php foreach ($models as $model): ?>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="story-item">
            <a href="<?= Url::toRoute(['/story/view', 'alias' => $model->alias]) ?>">
                <div class="story-item-image">
                    <?php if ($model->isAudioStory()): ?>
                        <span data-toggle="tooltip" title="В истории доступна озвучка" class="label label-danger story-label"><i class="glyphicon glyphicon-volume-up"></i></span>
                    <?php endif ?>
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
                    <p>
                        <h3 class="story-item-name"><?= Html::encode($model->title) ?></h3>
                        <?php
                        $categories = $model->categories;
                        $categoryName = '';
                        if (count($categories) > 0) {
                            $categoryName = $categories[0]->name;
                        }
                        ?>
                        <span class="story-item-category"><?= $categoryName ?></span>
                    </p>
                </div>
            </a>
        </div>
    </div>
    <?php endforeach ?>
</div>
