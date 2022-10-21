<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $model common\models\Story */
/** @var $playlist common\models\Playlist */
?>
<div id="story_wrapper">
    <?php if (Yii::$app->user->isGuest): ?>
    <noindex>
        <div class="alert alert-info story-wrapper-guest">
            <h2>Режим обучения доступен только авторизованным пользователям</h2>
            <p>Чтобы продолжить просмотр <a href="<?= Url::to(['/signup/request']) ?>">зарегистрируйтесь</a> или <a href="<?= Url::to(['/auth/login']) ?>">войдите в аккаунт</a></p>
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
        <?php if ($model->isAudioStory() || $model->isUserAudioStory(Yii::$app->user->id)): ?>
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
    <div class="row row-no-gutters">
        <?php if ($playlist === null): ?>
        <div class="col-md-12">
            <div class="story-container">
                <div class="story-container-inner" id="story-container">
                    <div class="story-no-subscription"><span class="story-loader">Загрузка истории...</span></div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="col-md-9">
            <div class="story-container">
                <div class="story-container-inner" id="story-container">
                    <div class="story-no-subscription"><span class="story-loader">Загрузка истории...</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="playlist-stories-wrapper">
                <h3 class="playlist-title"><?= $playlist->title ?></h3>
                <div class="playlist-stories">
                    <?php foreach ($playlist->stories as $story): ?>
                    <div class="media playlist-story">
                        <?php if ($story->id === $model->id): ?>
                            <span class="playlist-story-active"><i class="glyphicon glyphicon-play"></i></span>
                        <?php endif ?>
                        <div class="media-left">
                            <a href="<?= \yii\helpers\Url::to(['story/view', 'alias' => $story->alias, 'list' => $playlist->id]) ?>">
                                <?= Html::img($story->getBaseModel()->getCoverRelativePath(), ['height' => 64]) ?>
                            </a>
                        </div>
                        <div class="media-body">
                            <h4><?= $story->title ?></h4>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <?php endif ?>
    </div>
    <?php endif ?>
</div>
