<?php
use yii\helpers\Html;
/** @var $model common\models\Story */
?>
<div>
    <h1><?= Html::encode($model->title) ?></h1>
    <div class="story-description">
        <div class="story-categories">
            <?php foreach ($model->categories as $category): ?>
                <?= Html::a($category->name, $category->getCategoryUrl()) ?>
            <?php endforeach ?>
        </div>
        <?php if (!empty($model->description)): ?>
            <div class="story-text"><?= Html::encode($model->description) ?></div>
        <?php endif ?>
        <?php $facts = $model->storyFacts(); ?>
        <?php if (count($facts) > 0): ?>
            <div class="story-facts" style="font-size: 1.5rem">
                Из истории вы узнаете про:
                <?php $i = 1; ?>
                <?php foreach ($facts as $fact): ?>
                    <span class="label label-success" style="display: <?= $i <= 5 ? 'inline-block' : 'none' ?>"><?= $fact['title'] ?></span>
                    <?php $i++; ?>
                <?php endforeach ?>
                <?php if (($more = count($facts) - 5) > 0): ?>
                    <span class="label label-default more-facts" data-toggle="tooltip" title="Показать остальные факты" style="cursor: pointer">+ <?= $more ?></span>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>
</div>