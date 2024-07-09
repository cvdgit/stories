<?php

declare(strict_types=1);

use common\widgets\ToastrFlash;
use frontend\assets\NewSchoolAsset;
use frontend\widgets\ContactWidget;
use frontend\widgets\LoginWidget;
use frontend\widgets\SchoolMainMenuWidget;
use frontend\widgets\SignupWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use common\rbac\UserRoles;
use yii\web\View;

NewSchoolAsset::register($this);

/**
 * @var string $content
 * @var View $this
 */
?>
<?php
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php
    $this->head() ?>
    <?= Yii::$app->params['metrica'] ?>
    <script>
        const WikidsConfig = {
            'user': {
                'isGuest': <?= Json::encode(Yii::$app->user->isGuest) ?>,
                'isModerator': <?= Json::encode(Yii::$app->user->can(UserRoles::ROLE_MODERATOR)) ?>
            }
        };
    </script>
</head>
<body>
<?php
$this->beginBody() ?>
<header class="header header--background">
    <div class="header__inner">
        <div class="container-lg p-0">
            <nav class="navbar navbar-dark navbar-expand-lg p-0">
                <div class="logo">
                    <a class="logo__link font-weight-bold" href="/"><img class="logo__image" src="/school/img/logo.svg"
                                                                         alt="logo">
                        Wikids</a>
                </div>
                <div id="main-menu" class="nav__wrap navbar-collapse collapse justify-content-center">
                    <ul class="navbar-nav align-items-center bg-light p-3 rounded-pill">
                        <li class="menu-item"><a class="text-dark mx-5 menu-item__link" href="#">Для кого</a></li>
                        <li class="menu-item"><a class="text-dark mx-5 menu-item__link" href="#">Преимущества</a></li>
                        <li class="menu-item"><a class="text-dark mx-5 menu-item__link" href="#">Сертификаты</a></li>
                    </ul>
                </div>
                <div>
                    <a class="contact-phone font-weight-bold" href="tel:+79262074146">+7 (926) 207−41−46</a>
                </div>
                <button class="navbar-toggler collapsed" data-toggle="collapse" data-target="#main-menu">
                    <span class="menu-icon"></span>
                </button>
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
                                    <a class="logo__link" href="/"><img class="logo__image" src="/school/img/logo.svg"
                                                                        alt="logo"> Wikids</a>
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
                                    <a target="_blank" class="social-row__link" href="https://vk.com/club184614838"><img
                                            src="/school/img/vk-social.svg" alt="vk"></a>
                                    <a target="_blank" class="social-row__link"
                                       href="https://zen.yandex.ru/id/5c975a093bbd5d00b3568680"><img
                                            src="/school/img/zen-social.svg" alt="zen"></a>
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
<?php
if (Yii::$app->user->isGuest): ?>
    <?= LoginWidget::widget() ?>
    <?= SignupWidget::widget() ?>
<?php
endif ?>
<?php
// echo ContactWidget::widget() ?>
<?php
// echo ToastrFlash::widget() ?>
<?php
$this->endBody() ?>
</body>
</html>
<?php
$this->endPage() ?>
