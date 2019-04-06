<?php

use yii\helpers\Html;
use common\services\StoryService;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $models Story[] */

?>
<div class="flex-row row story-list">
    <?php $storyService = new StoryService; ?>
    <?php foreach ($models as $model): ?>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="story-item">
        <a href="<?= Url::toRoute(['/story/view', 'alias' => $model->alias]) ?>">
          <div class="story-item-image">
            <div class="story-item-image-overlay">
              <span></span>
            </div>
            <?php $img = empty($model->cover) ? './img/story-1.jpg' : $storyService->getCoverPath($model->cover, true); ?>
            <?= Html::img($img) ?>
          </div>
          <div class="story-item-caption">
            <p class="flex-text"></p>
            <p>
              <span class="story-item-name"><?= Html::encode($model->title) ?></span>
              <span class="story-item-pay"><?= $model->bySubscription() ? 'По подписке' : 'Беслпатно' ?></span>
            </p>
          </div>
        </a>
      </div>
    </div>
    <?php endforeach ?>
</div>
