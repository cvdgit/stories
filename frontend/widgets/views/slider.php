<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\StoryCover;
?>
<div id="wikids-carousel" class="carousel slide" data-ride="carousel" data-interval="false">
  <div class="carousel-inner" role="listbox">
    <?php foreach ($models as $i => $model): ?>
    <div class="item <?= ($i == 0 ? 'active' : '') ?>">
      <a href="<?= Url::toRoute(['/story/view', 'alias' => $model->alias]) ?>">
        <div class="carousel-item-image">
          <div class="carousel-item-image-overlay">
            <span></span>
          </div>
          <?= Html::img(StoryCover::getStoryThumbPath($model->cover)) ?>
        </div>
        <div class="carousel-caption">
          <h3><?= Html::encode($model->title) ?></h3>
        </div>
      </a>
    </div>
    <?php endforeach ?>
  </div>
  <a class="left carousel-control" href="#wikids-carousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#wikids-carousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>