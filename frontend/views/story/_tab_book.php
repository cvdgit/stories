<div class="slides-readonly">
    <?php if ($model->isAudioStory()): ?>
    <div class="alert alert-info to-slides-tab noselect">
        <p>Озвучка доступна в режиме обучения</p>
    </div>
    <?php endif ?>
    <?php if (!empty($model->body)): ?>
        <?= $model->body ?>
    <?php else: ?>
        <p>Содержимое истории недоступно</p>
    <?php endif ?>
</div>