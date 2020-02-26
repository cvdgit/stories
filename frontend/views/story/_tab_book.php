<div class="slides-readonly">
    <?php if ($model->isAudioStory()): ?>
    <div class="alert alert-info">
        <p>Озвучка доступна в режиме обучения</p>
    </div>
    <?php endif ?>
    <?php if ($model->haveVideo()): ?>
        <div class="alert alert-info">
            <p>Видео доступно в режиме обучения</p>
        </div>
    <?php endif ?>
    <?php if (!empty($model->body)): ?>
        <?php if (Yii::$app->user->isGuest): ?>
        <?= $model->storyPreview() ?>
        <noindex>
            <div class="jumbotron">
                <div class="container-fluid text-center">
                    <h2>Ознакомительный просмотр завершен</h2>
                    <p style="font-weight: 300">Чтобы продолжить просмотр истории необходимо зарегистрироваться</p>
                    <img width="100%" src="/img/random-story.jpg" alt="" style="margin-top: 30px">
                    <div class="text-center" style="margin: 40px 0 0 0">
                        <a class="btn" href="#" data-toggle="modal" data-target="#wikids-signup-modal">Зарегистрироваться</a> или
                        <a class="btn" href="#" data-toggle="modal" data-target="#wikids-login-modal">Войти</a>
                    </div>
                </div>
            </div>
        </noindex>
        <?php else: ?>
        <?= $model->body ?>
        <?php endif ?>
    <?php else: ?>
        <p>Содержимое истории недоступно</p>
    <?php endif ?>
</div>
