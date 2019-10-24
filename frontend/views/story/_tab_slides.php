<div id="story_wrapper">
    <div class="story-container">
        <div class="story-container-inner" id="story-container">
            <div class="story-no-subscription"><span class="story-loader">Загрузка истории...</span></div>
        </div>
    </div>
    <?php if (Yii::$app->user->can('moderator')): ?>
        <?= \frontend\widgets\RecorderWidget::widget(['story' => $model]) ?>
    <?php endif ?>
</div>
