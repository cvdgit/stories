<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Menu;
use common\helpers\Url;

\frontend\assets\AppAsset::register($this);

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
</head>
<body>
<?php $this->beginBody() ?>
  <header class="<?= Url::isHome() ? 'site-header' : 'site-header-mini' ?>">
    <nav class="site-nav">
      <div class="container">
        <div class="row">
          <div class="col-sm-4 col-md-3">
            <?php 
            $options = ['class' => 'site-logo', 'alt' => 'wikids', 'title' => 'wikids'];
            if (Url::isHome())
              echo Html::img('/img/wikids.png', $options);
            else
              echo Html::a(Html::img('/img/wikids-mini.png', $options), ['/site/index']);
            ?>
          </div>
          <div class="col-sm-5 col-md-6">
            <div class="menu-wrapper">
              <?php
              $menuItems = [
                  ['label' => 'Главная', 'url' => ['/site/index']],
                  ['label' => 'Истории', 'url' => ['/story/index'], 'active' => (Yii::$app->controller->id === 'story')],
                  ['label' => 'Подписки', 'url' => ['/rate/index']],
                  ['label' => 'Контакты', 'url' => '#', 'template'=> '<a href="{url}" data-toggle="modal" data-target="#wikids-feedback-modal">{label}</a>'],
              ];
              echo Menu::widget([
                  'encodeLabels' => false,
                  'items' => $menuItems,
                  'options' => ['class' => 'site-menu site-main-menu horizontal-nav'],
              ]);
              ?>
            </div>
          </div>
          <div class="col-sm-3 col-md-3">
            <div class="user-menu-wrapper">
              <?php
              if (Yii::$app->user->isGuest) {
                echo Html::a('Регистрация', '#', ['data-toggle' => 'modal', 'data-target' => '#wikids-signup-modal']) . 
                     Html::tag('span') .
                     Html::a('Войти', '#', ['class' => 'login-item', 'data-toggle' => 'modal', 'data-target' => '#wikids-login-modal']);
              }
              else {
                echo Html::a(Yii::$app->user->identity->getProfileName(), ['/profile/index']) .
                     Html::tag('span') .
                     Html::beginForm(['/auth/logout'], 'post') .
                     Html::submitButton('Выход', ['class' => 'login-item logout-btn-a']) . 
                     Html::endForm();
              }
              ?>
            </div>
            <?php if (Url::isHome()): ?>
            <span class="site-phone-number">+7 (499) 703-35-25</span>
            <?php endif ?>
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
          <?= \frontend\widgets\StorySlider::widget() ?>
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
          <a class="footer-link" href="#!">Wikids</a>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3 footer-email">
            Телефон: +7 (499) 703-3525<br>
            Email: <a href="mailto:info@centrvd.ru">info@centrvd.ru</a>
        </div>
        <div class="col-md-3 col-md-offset-6">
          <div class="footer-copyright pull-right text-right">
              Wikids © 2019<br>
              ИП Муталов Артур Сагадеевич
          </div>
        </div>
      </div>
    </div>
  </footer>
  <?php if (Yii::$app->user->isGuest): ?>
  <?= \frontend\widgets\LoginWidget::widget() ?>
  <?= \frontend\widgets\SignupWidget::widget() ?>
  <?php endif ?>
  <?= \frontend\widgets\ContactWidget::widget() ?>
  <?= \common\widgets\ToastrFlash::widget() ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
