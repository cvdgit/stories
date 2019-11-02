<div id="story_wrapper">
    <?php if (Yii::$app->user->isGuest): ?>
    <div class="alert alert-info story-wrapper-guest">
        <h2>Режим обучения доступен только авторизованным пользователям</h2>
        <p>Чтобы продолжить просмотр <a data-toggle="modal" data-target="#wikids-signup-modal" href="#">зарегистрируйтесь</a> или <a data-toggle="modal" data-target="#wikids-login-modal" href="#">войдите в аккаунт</a></p>
    </div>
    <div class="jumbotron wikids-jumbotron">
        <p class="text-center">Возможности режима обучения:</p>
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
