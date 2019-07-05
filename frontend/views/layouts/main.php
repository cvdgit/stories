<?php

/* @var $this yii\web\View */
/* @var $content string */

use common\rbac\UserRoles;
use common\widgets\ToastrFlash;
use frontend\assets\AppAsset;
use frontend\widgets\ContactWidget;
use frontend\widgets\LoginWidget;
use frontend\widgets\SignupWidget;
use frontend\widgets\StorySlider;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\widgets\Menu;
use common\helpers\Url;

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
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?= Yii::$app->params['metrica'] ?>
</head>
<body>
<?php $this->beginBody() ?>
    <header class="site-header-main <?= Url::isHome() ? 'site-header' : 'site-header-mini' ?>">
        <?php $isStoryViewPage = Yii::$app->controller->id === 'story' && Yii::$app->controller->action->id === 'view'; ?>
        <nav class="site-nav  <?= $isStoryViewPage ? 'story-view' : '' ?>">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <?php
                    $options = ['class' => 'site-logo', 'alt' => 'wikids', 'title' => 'wikids'];
                    if (Url::isHome())
                      echo Html::img('/img/wikids.png', $options);
                    else
                      echo Html::a(Html::img('/img/wikids-mini.png', $options), ['/site/index']);
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
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <div class="menu-wrapper">
                        <?php
                        $menuItems = [
                          ['label' => 'Главная', 'url' => ['/site/index']],
                          ['label' => 'Истории', 'url' => ['/story/index'], 'active' => Yii::$app->controller->id === 'story'],
                          ['label' => 'Блог', 'url' => ['news/index'], 'active' => Yii::$app->controller->id === 'news'],
                          ['label' => 'Подписки', 'url' => ['/rate/index']],
                          ['label' => 'Контакты', 'url' => '#', 'template'=> '<a href="{url}" data-toggle="modal" data-target="#wikids-feedback-modal">{label}</a>'],
                        ];
                        echo Menu::widget([
                          'encodeLabels' => false,
                          'items' => $menuItems,
                          'options' => ['class' => 'site-menu site-main-menu horizontal-nav collapse'],
                        ]);
                        ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                        <div class="user-menu-wrapper">
                        <?php if (Yii::$app->user->isGuest): ?>
                            <div class="user-menu-inner">
                                <?= Html::a('Регистрация', '#', ['data-toggle' => 'modal', 'data-target' => '#wikids-signup-modal']) ?>
                                <span></span>
                                <?= Html::a('Войти', '#', ['class' => 'login-item', 'data-toggle' => 'modal', 'data-target' => '#wikids-login-modal']) ?>
                            </div>
                        <?php else: ?>
                            <div class="profile-photo pull-right">
                                <?= Html::img(Yii::$app->user->identity->getProfilePhoto()) ?>
                            </div>
                            <div class="dropdown">
                                <div style="cursor: pointer" data-toggle="dropdown" class="dropdown-toggle"><b class="caret"></b> <?= Yii::$app->user->identity->getProfileName() ?></div>
                                <?= Dropdown::widget(['items' => [
                                        ['label' => 'Профиль', 'url' => ['/profile/index']],
                                        ['label' => 'История просмотра', 'url' => ['/story/history']],
                                        ['label' => 'Панель управления', 'url' => '/admin', 'visible' => Yii::$app->user->can(UserRoles::PERMISSION_ADMIN_PANEL)],
                                        ['label' => Html::beginForm(['/auth/logout']) .
                                 Html::submitButton('Выход', ['class' => 'login-item logout-btn-a']) .
                                 Html::endForm(),
                                            'encode' => false,
                                        ],
                                ], 'options' => ['class' => 'dropdown-menu-right']]) ?>
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
          <h1>Сервис ускоренного развития речи ребенка</h1>
          <p>Посмотрите короткий ролик о сервисе или зарегистрируйтесь</p>
          <?php if (Yii::$app->user->isGuest): ?>
          <?= Html::a('Регистрация', '#', ['class' => 'btn', 'data-toggle' => 'modal', 'data-target' => '#wikids-signup-modal']) ?>
          <?php else: ?>
          <?= Html::a('Каталог историй', ['/story/index'], ['class' => 'btn']) ?>
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
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-3 footer-email">Телефон: +7 (499) 703-3525<br>Email: <a href="mailto:info@wikids.ru">info@wikids.ru</a></div>
                <div class="col-sm-12 col-md-6 footer-links">
                    <?= Html::a('Политика конфиденциальности', ['site/policy']) ?>
                    <?= Html::a('Правообладателям', ['site/copyright']) ?>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="footer-copyright pull-right text-right">Wikids © 2019<br>ИП Муталов Артур Сагадеевич</div>
                </div>
            </div>
        </div>
    </footer>
  <?php if (Yii::$app->user->isGuest): ?>
  <?= LoginWidget::widget() ?>
  <?= SignupWidget::widget() ?>
  <?php endif ?>
  <?= ContactWidget::widget() ?>
  <?= ToastrFlash::widget() ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
