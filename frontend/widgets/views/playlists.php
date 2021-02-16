<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $models common\models\Playlist[] */
?>
<section class="site-playlists">
    <h2 class="container">Популярные <span>плейлисты</span></h2>
    <div class="container">
        <div class="flex-row row story-list">
            <?php foreach ($models as $model): ?>
            <?php if (count($model->stories) > 0): ?>
            <?php $story = $model->stories[0]; ?>
            <div class="col-xs-offset-1 col-xs-10 col-sm-offset-0 col-sm-6 col-md-offset-0 col-md-6 col-lg-offset-0 col-lg-3">
                <div class="story-item">
                    <a rel="nofollow" href="<?= Url::toRoute(['/story/view', 'alias' => $story->alias, 'list' => $model->id]) ?>">
                        <div class="story-item-image">
                            <div class="story-item-image-overlay">
                                <span></span>
                            </div>
                            <img src="<?= $story->getBaseModel()->getCoverRelativePath() ?>" alt="">
                        </div>
                        <div class="story-item-caption">
                            <p class="flex-text"></p>
                            <p>
                                <h3 class="story-item-name"><?= Html::encode($model->title) ?></h3>
                                <span class="story-item-category"> Историй: <?= count($model->stories) ?></span>
                            </p>
                        </div>
                    </a>
                </div>
            </div>
            <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
</section>
