<?php

use yii\helpers\Url;

/** @var $models common\models\Playlist[] */
?>
<div class="row">
    <?php foreach ($models as $model): ?>
    <?php $story = $model->stories[0]; ?>
    <div class="col-xs-offset-1 col-xs-10 col-sm-offset-0 col-sm-6 col-md-offset-0 col-md-6 col-lg-offset-0 col-lg-3">
        <div class="category-item">
            <a href="<?= Url::toRoute(['/story/view', 'alias' => $story->alias, 'list' => $model->id]) ?>">
                <div class="category-item-image-wrapper">
                    <img src="<?php // $story->getBaseModel()->getCoverRelativePath() ?>" alt="">
                </div>
                <h3><?= $model->title ?></h3>
            </a>
        </div>
    </div>
    <?php endforeach ?>
</div>
