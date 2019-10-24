<?php
use frontend\widgets\StoryAudio;
?>
<div class="slides-readonly">
    <?php if ($model->isAudioStory()): ?>
        <?= StoryAudio::widget(['storyID' => $model->id]) ?>
    <?php endif ?>
    <?php if (!empty($model->body)): ?>
        <?= $model->body ?>
    <?php else: ?>
        <p>Содержимое истории недоступно</p>
    <?php endif ?>
</div>
