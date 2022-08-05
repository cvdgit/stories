<?php

use common\widgets\ToastrFlash;
use frontend\assets\SchoolAsset;
use frontend\widgets\ContactWidget;
use frontend\widgets\LoginWidget;
use frontend\widgets\SignupWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use common\rbac\UserRoles;
use yii\widgets\Menu;

SchoolAsset::register($this);

/** @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <header class="header header--background">
        <div class="header__inner">
            <div class="container-lg p-0">
                <nav class="navbar navbar-dark navbar-expand-lg">
                    <div class="logo mr-auto">
                        <a class="logo__link" href="/"><img class="logo__image" src="/school/img/logo.svg" alt="logo"> Wikids</a>
                    </div>
                    <button class="navbar-toggler collapsed" data-toggle="collapse" data-target="#main-menu">
                        <span class="menu-icon"></span>
                    </button>
                    <div id="main-menu" class="nav__wrap navbar-collapse collapse">
                        <?php
                        $items = [
                            [
                                'label' => '<span class="menu-item__link dropdown-toggle">Разделы</span>',
                                'items' => [
                                    ['label' => 'Истории для детей', 'url' => ['/story/index', 'section' => 'stories']],
                                    ['label' => 'DIRECTUM', 'url' => ['/story/index', 'section' => 'directum']]
                                ],
                                'options' => ['class' => 'menu-item dropdown'],
                                'submenuTemplate' => "\n<ul class='dropdown-menu'>\n{items}\n</ul>\n",
                            ],
                            ['label' => 'Блог', 'url' => ['/news/index'], 'active' => Yii::$app->controller->id === 'news'],
                            ['label' => 'Обучение', 'url' => ['/edu/default/index']],
                            ['label' => 'Контакты', 'url' => '#', 'template'=> '<a class="menu-item__link" href="{url}" data-toggle="modal" data-target="#wikids-feedback-modal">{label}</a>'],
                        ];
                        if (Yii::$app->user->isGuest) {
                            $items[] = [
                                'label' => 'Войти',
                                'url' => '#',
                                'template' => Html::a('{label}', '{url}', ['class' => 'menu-item__link', 'data-toggle' => 'modal', 'data-target' => '#wikids-login-modal']),
                            ];
                            $items[] = [
                                'label' => 'Регистрация',
                                'url' => '#',
                                'template' => Html::a('{label}', '{url}', ['class' => 'registration-link', 'data-toggle' => 'modal', 'data-target' => '#wikids-signup-modal']),
                            ];
                        }
                        else {
                            $items[] = [
                                'label' => '<div class="profile-row dropdown-toggle" data-toggle="dropdown">
                                        <div class="profile-row__name">' . Yii::$app->user->identity->getProfileName() . '</div>
                                        <div class="profile-row__image">
                                            ' . Html::img(Yii::$app->user->identity->getProfilePhoto(), ['class' => 'profile-image', 'alt' => 'pic']) . '
                                        </div>
                                    </div>',
                                'items' => [
                                    ['label' => 'Профиль', 'url' => ['/profile/index'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                                    ['label' => 'История просмотра', 'url' => ['/story/history'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                                    ['label' => 'Любимые истории', 'url' => ['/story/liked'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                                    ['label' => 'Избранные истории', 'url' => ['/story/favorites'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                                    ['label' => 'Панель управления', 'url' => '/admin', 'visible' => Yii::$app->user->can(UserRoles::PERMISSION_ADMIN_PANEL), 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                                    ['label' => Html::beginForm(['/auth/logout']) .
                                        Html::submitButton('Выйти', ['class' => 'dropdown-item menu-item__link']) .
                                        Html::endForm(),
                                        'encode' => false,
                                    ],
                                ],
                                'options' => ['class' => 'menu-item'],
                                'submenuTemplate' => "\n<div class='user-profile dropdown'>\n<ul class='dropdown-menu dropdown-menu-right'>\n{items}\n</ul>\n</div>\n",
                            ];
                        }
                        echo Menu::widget([
                            'items' => $items,
                            'options' => ['class' => 'navbar-nav align-items-center'],
                            'itemOptions' => ['class' => 'menu-item'],
                            'linkTemplate' => Html::a('{label}', '{url}', ['class' => 'menu-item__link']),
                            'encodeLabels' => false,
                        ]);
                        ?>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <?= $content ?>
    <footer class="footer">
        <div class="container-fluid p-0">
            <div class="container-lg p-0">
                <div class="footer-main">
                    <div class="footer-row">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-6">
                                <div class="footer-left">
                                    <div class="logo">
                                        <a class="logo__link" href="/"><img class="logo__image" src="/school/img/logo.svg" alt="logo"> Wikids</a>
                                    </div>
                                    <div class="contact-row">
                                        <a class="contact-row__phone" href="tel:+74997033525">+7 (926) 207−41−46</a>
                                    </div>
                                    <div class="contact-row contact-row--no-mb">
                                        <a class="contact-row__email" href="mailto:info@wikids.ru">info@wikids.ru</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="footer-right">
                                    <div class="social-row">
                                        <a target="_blank" class="social-row__link" href="https://vk.com/club184614838"><img src="/school/img/vk-social.svg" alt="vk"></a>
                                        <a target="_blank" class="social-row__link" href="https://zen.yandex.ru/id/5c975a093bbd5d00b3568680"><img src="/school/img/zen-social.svg" alt="zen"></a>
                                    </div>
                                    <div class="contact-row">
                                        <p class="contact-row__text">© 2018–<?= date('Y') ?>, Wikids</p>
                                    </div>
                                    <div class="contact-row contact-row--no-mb">
                                        <p class="contact-row__text">ООО «ЦВД»</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="footer-links__wrap">
                        <?= Html::a('Политика конфиденциальности', ['/site/policy'], ['class' => 'footer-links__link']) ?>
                        <?= Html::a('Правообладателям', ['/site/copyright'], ['class' => 'footer-links__link']) ?>
                    </div>
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
