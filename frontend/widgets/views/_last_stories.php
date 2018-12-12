<?php

use yii\helpers\Html;
use common\services\StoryService;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $models Story[] */

?>
<div class="grid-product">
    <div class="title-head">
        <h2 class="text-black">Новые истории</h2>
    </div>
    <?php $i = 0; $len = sizeof($models) - 1; ?>
    <?php $storyService = new StoryService; ?>
    <?php foreach ($models as $model): ?>
    <?php $first = $i == 0; $last = $i == $len; $endline = !$first && $i % 4 == 0; ?>
    <?php if ($first || $endline): ?>
    <?php if ($endline): ?>
    </div>
    <?php endif; ?>
    <div class="row">
    <?php endif; ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="product">
                <a href="<?= Url::toRoute(['/story/view', 'alias' => $model->alias]) ?>">
                    <div class="images text-center">
                        <?php $img = empty($model->cover) ? 'http://via.placeholder.com/180x210' : $storyService->getCoverPath($model->cover, true); ?>
                        <?= Html::img($img) ?>
                        <div class="button-group">
                            <span class="custom-btn pink"><i class="fa fa-play"></i></span>
                            <p class="cst-stories-type">
                                <?= ($model->sub_access) ? 'Смотреть по подписке' : 'Смотреть бесплатно' ?>
                            </p>
                        </div>
                    </div>
                </a>
                <a href="<?= Url::toRoute(['/story/view', 'alias' => $model->alias]) ?>">
                    <div class="info-product">
                        <?= Html::tag('p', Html::encode($model->title), ['class' => 'title']) ?>
                        <p class="cst-p-grey"><?= ($model->sub_access) ? 'Подписка' : 'Бесплатно' ?></p>
                    </div>
                </a> 
            </div>
        </div>
    <?php $i++; ?>
    <?php if ($last): ?>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
    <div class="text-center">
        <?= Html::a('Посмотреть все истории', ['/story/index'], ['class' => 'custom-btn text-center']) ?>
    </div>
</div>