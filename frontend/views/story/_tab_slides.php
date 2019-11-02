<div id="story_wrapper">
    <?php if (Yii::$app->user->isGuest): ?>
    <div class="jumbotron wikids-jumbotron">
        <div class="text-center">
            <h2>Режим обучения доступен только авторизованным пользователям</h2>
            <p>Чтобы продолжить просмотр зарегистрируйтесь или войдите в аккаунт</p>
            <br>
            <p>Возможности режима обучения:</p>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <ul>
                    <li>просмотр истории в виде слайдов</li>
                    <li>возможность прослушивания озвучки по каждому слайду</li>
                    <li>возможность добавить свою, детскую озвучку</li>
                    <li>тесты для детей, чтобы закрепить материал</li>
                    <li>специально подобранные коллекции картинок и видео для улучшения восприятия</li>
                    <li>ссылки на дополнительные обучающие курсы</li>
                </ul>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6 text-center">
                <button class="btn" data-toggle="modal" data-target="#wikids-signup-modal">Зарегистрироваться</button>
            </div>
            <div class="col-md-6 text-center">
                <button class="btn" data-toggle="modal" data-target="#wikids-login-modal">Войти</button>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="story-container">
        <div class="story-container-inner" id="story-container">
            <div class="story-no-subscription"><span class="story-loader">Загрузка истории...</span></div>
        </div>
    </div>
    <?php endif ?>
    <?php if (Yii::$app->user->can('moderator')): ?>
        <?= \frontend\widgets\RecorderWidget::widget(['story' => $model]) ?>
    <?php endif ?>
</div>
