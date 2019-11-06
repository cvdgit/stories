<div id="story_wrapper">
    <?php if (Yii::$app->user->isGuest): ?>
    <noindex>
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
    </noindex>
    <?php else: ?>
    <?php if ($model->isAudioStory()): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <span>Автовоспроизведение</span>
                <form class="form" id="autoplay-form" style="display: inline-block">
                    <div class="switch-field">
                        <input type="radio" id="autoplay-yes" name="autoplay" value="yes" />
                        <label for="autoplay-yes">Да</label>
                        <input type="radio" id="autoplay-no" name="autoplay" value="no" />
                        <label for="autoplay-no">Нет</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif ?>
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
