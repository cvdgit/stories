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
                    <div class="story-item-image-overlay">
                        <span></span>
                    </div>
                    <?php $img = empty($model->cover) ? '/img/story-1.jpg' : StoryCover::getListThumbPath($model->cover); ?>
                    <?= Html::img($img) ?>
                </div>
                <div class="story-item-caption">
                    <p class="flex-text"></p>
                    <p>
                        <h3 class="story-item-name"><?= Html::encode($model->title) ?><?= $model->isAudioStory() ? ' <i title="В истории доступна озвучка" class="glyphicon glyphicon-volume-up"></i>' : '' ?></h3>
                        <?php
                        $categories = $model->categories;
                        $categoryName = '';
                        if (count($categories) > 0) {
                            $categoryName = $categories[0]->name;
                        }
                        ?>
                        <span class="story-item-category"><?= $categoryName ?></span>
                        <?php if (!Yii::$app->user->isGuest): ?>
                        <?php if ($model->bySubscription()): ?>
                        <span class="story-item-pay">По подписке</span>
                        <?php endif ?>
                        <?php endif ?>
                    </p>
                </div>
            </a>
        </div>
    </div>
    <?php endforeach ?>
</div>
