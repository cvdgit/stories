<?php

declare(strict_types=1);

use common\rbac\UserRoles;
use common\widgets\ToastrFlash;
use frontend\assets\AppAsset;
use frontend\widgets\ContactWidget;
use frontend\widgets\MainMenuWidget;
use frontend\widgets\StorySlider;
use frontend\widgets\UserNotification;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\helpers\Json;
use common\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?= Yii::$app->params['metrica'] ?>
    <script>
        var WikidsConfig = {
            'user': {
                'isGuest': <?= Json::encode(Yii::$app->user->isGuest) ?>,
                'isModerator': <?= Json::encode(Yii::$app->user->can(UserRoles::ROLE_MODERATOR)) ?>
            }
        };
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<?php $isStoryViewPage = Yii::$app->controller->id === 'story' && Yii::$app->controller->action->id === 'view'; ?>
    <header class="site-header-main <?= Url::isHome() ? 'site-header' : 'site-header-mini' ?> <?= $isStoryViewPage ? 'story-view-header' : '' ?>">
        <nav class="site-nav  <?= $isStoryViewPage ? 'story-view' : '' ?>">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
                    <?php
                    $options = ['class' => 'site-logo', 'alt' => 'wikids', 'title' => 'wikids'];
                    if (Url::isHome()) {
                        echo Html::img('/img/wikids.png', $options);
                    }
                    else {
                        echo Html::a(Html::img('/img/wikids-mini.png', $options), Url::homeRoute());
                    }
                    ?>
                        <div style="position: relative; float: right">
                            <button type="button" class="navbar-toggle navbar-user" data-toggle="collapse" data-target=".user-menu-wrapper"></button>
                            <button type="button" class="navbar-toggle navbar-main-menu" data-toggle="collapse" data-target=".menu-wrapper">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="menu-wrapper">
                            <?= MainMenuWidget::widget() ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3">
                        <div class="user-menu-wrapper">
                        <?php if (Yii::$app->user->isGuest): ?>
                            <div class="user-menu-inner">
                                <?= Html::a('Регистрация', ['/signup/request'], ['onclick' => "ym(53566996, 'reachGoal', 'show_registration_form'); return true;"]) ?>
                                <span class="delimiter"></span>
                                <?= Html::a('Войти', ['/auth/login'], ['class' => 'login-item']) ?>
                            </div>
                        <?php else: ?>
                            <?= UserNotification::widget() ?>
                            <div class="dropdown pull-right">
                                <div style="cursor: pointer" data-toggle="dropdown" class="dropdown-toggle profile-photo">
                                    <?= Html::img(Yii::$app->user->identity->getProfilePhoto()) ?>
                                </div>
                                <?= Dropdown::widget(['items' => [
                                        ['label' => 'Профиль', 'url' => ['/profile/index']],
                                        ['label' => 'История просмотра', 'url' => ['/story/history']],
                                        ['label' => 'Любимые истории', 'url' => ['/story/liked']],
                                        ['label' => 'Избранные истории', 'url' => ['/story/favorites']],
                                        ['label' => 'Повторения', 'url' => ['/my-repetition/index']],
                                        ['label' => 'Панель управления', 'url' => '/admin', 'visible' => Yii::$app->user->can(UserRoles::PERMISSION_ADMIN_PANEL)],
                                        ['label' => Html::beginForm(['/auth/logout']) .
                                 Html::submitButton('Выход', ['class' => 'login-item logout-btn-a']) .
                                 Html::endForm(),
                                            'encode' => false,
                                        ],
                                ], 'options' => ['class' => 'user-menu-dropdown pull-right']]) ?>
                            </div>
                        <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    <?php if (Url::isHome()): ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-6 site-offer">
                <?php if (Yii::$app->user->isGuest): ?>
                    <h1>Сервис ускоренного развития речи ребенка</h1>
                    <p>Зарегистрируйтесь, чтобы получить доступ ко всем возможностям:</p>
                    <ul>
                        <li>аудиоистории - прослушивание озвучки к каждому слайду</li>
                        <li>самостоятельное озвучивание историй</li>
                        <li>тесты для детей, чтобы закрепить материал</li>
                        <li>просмотр истории в виде слайдов</li>
                        <li>специально подобранные коллекции картинок и видео для улучшения восприятия</li>
                        <li>ссылки на дополнительные обучающие курсы</li>
                    </ul>
                    <div class="text-center">
                        <?= Html::a('Зарегистрироваться', '#wikids-signup-modal', ['class' => 'btn', 'data-toggle' => 'modal', 'style' => 'margin: 10px 0']) ?>
                        <p style="font-size: .889em">При регистрации подписка на 1 год бесплатно ;)</p>
                    </div>
                <?php else: ?>
                    <h1 class="header-auth">Сервис ускоренного развития речи ребенка</h1>
                    <p>Используйте все возможности Wikids:</p>
                    <ul>
                        <li>аудиоистории - прослушивание озвучки к каждому слайду</li>
                        <li>самостоятельное озвучивание историй</li>
                        <li>тесты для детей, чтобы закрепить материал</li>
                        <li>просмотр истории в виде слайдов</li>
                        <li>специально подобранные коллекции картинок и видео для улучшения восприятия</li>
                        <li>ссылки на дополнительные обучающие курсы</li>
                    </ul>
                    <div class="text-center">
                        <?= Html::a('Каталог историй', ['/story/index', 'section' => 'stories'], ['class' => 'btn']) ?>
                    </div>
                <?php endif ?>
        </div>
        <div class="col-md-12 col-lg-6 site-slider">
          <?= StorySlider::widget() ?>
        </div>
      </div>
    </div>
    <?php endif ?>
  </header>
  <?= $content ?>
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                <?php if (Url::isHome()): ?>
                <span class="footer-link">Wikids</span>
                <?php else: ?>
                <a class="footer-link" href="/">Wikids</a>
                <?php endif ?>
                </div>
                <div class="col-md-6">
                    <a href="https://zen.yandex.ru/id/5c975a093bbd5d00b3568680" class="footer-link-share" target="_blank"><img width="36" height="36" src="/img/zen-icon.png" alt="zen"></a>
                    <a href="https://vk.com/club184614838" class="footer-link-share" target="_blank"><img width="36" height="36" src="/img/vk-icon.png" alt="vk"></a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-3 footer-email">Телефон: +7 (499) 703-3525<br>Email: <a href="mailto:info@wikids.ru">info@wikids.ru</a></div>
                <div class="col-sm-12 col-md-6 footer-links">
                    <?= Html::a('Политика конфиденциальности', ['/site/policy']) ?>
                    <?= Html::a('Правообладателям', ['/site/copyright']) ?>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="footer-copyright pull-right text-right">©&nbsp;2018—<?= date('Y') ?>,&nbsp;Wikids<br>ИП Муталов Артур Сагадеевич</div>
                </div>
            </div>
        </div>
    </footer>
    <?= ContactWidget::widget() ?>
    <?= ToastrFlash::widget() ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
