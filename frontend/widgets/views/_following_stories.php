<?php

use yii\helpers\Html;
use common\components\StoryCover;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $models common\models\Story[] */

?>
<div class="flex-row row story-list">
    <?php foreach ($models as $model): ?>
        <div class="col-xs-6 col-sm-6 col-md-12 col-lg-6">
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
                            <?php if (!Yii::$app->user->isGuest): ?>
                            <span class="story-item-pay"><?= $model->bySubscription() ? 'По подписке' : 'Бесплатно' ?></span>
                            <?php endif ?>
                        </p>
                    </div>
                </a>
            </div>
        </div>
    <?php endforeach ?>
</div>
