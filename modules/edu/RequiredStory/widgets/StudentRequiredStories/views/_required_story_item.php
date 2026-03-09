<?php

declare(strict_types=1);

use modules\edu\RequiredStory\widgets\StudentRequiredStories\WidgetRequiredStory;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var WidgetRequiredStory $model
 */
?>
<div class="col-lg-3 col-md-4 col-sm-6">
    <div class="story-item edu-story-item">
        <a class="run-story"
           href="<?= Url::toRoute(['/edu/story/view', 'id' => $model->getStoryId()]) ?>"
           data-pjax="0" style="gap: 10px">
            <div class="story-item-image">
                <div class="story-image" style="background-image: url(<?= $model->getStoryCover() ?>)">
                    <div class="story-image__icon"></div>
                </div>
                <div class="story-item-image-overlay">
                    <span></span>
                </div>
            </div>
            <div class="story-item-caption">
                <h3 class="story-item-name"><?= Html::encode($model->getStoryTitle()) ?></h3>
                <?php if ($model->getSession() !== null): ?>
                    <p class="story-item-category" style="display: flex; font-size: 1.5rem; justify-content: center; flex-direction: row; gap: 10px; align-items: center">Ответов сегодня: <?= $model->getSession()->getFact() ?> из <?= $model->getSession()->getPlan() ?> <?= $model->getSession()->isCompleted() ? '<span class="label label-success">Выполнено</span>' : '' ?></p>
                <?php endif ?>
            </div>
        </a>
    </div>
</div>
