<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
//use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\assets\FontAwesomeAsset;
use common\widgets\Alert;
use common\components\StoryNav;

AppAsset::register($this);
//FontAwesomeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.5">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<?php
$controller = Yii::$app->controller;
$isStoryViewPage = ($controller->id === 'story' && $controller->action->id === 'view');
?>
<body <?= ($isStoryViewPage ? 'class="single-product"' : '') ?>>
<?php $this->beginBody() ?>
<div class="wrapper">
    <header>
        <div class="top-bar bg-black">
            <div class="container-large">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12 text-left">
                        <ul>
                            <li><a href="contact.html">Contact Us</a></li>
                            <li><a href="contact.html">Support</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                        <?php
                        $menuItems = [];
                        if (Yii::$app->user->isGuest) {
                            $menuItems[] = ['label' => 'Вход', 'url' => ['/site/login']];
                            $menuItems[] = ['label' => 'Регистрация', 'url' => ['/site/signup']];
                        } else {
                            $menuItems[] = ['label' => 'Профиль', 'url' => ['/profile/index']];
                            $menuItems[] = '<li>'
                                . Html::beginForm(['/site/logout'], 'post')
                                . Html::submitButton(
                                    'Выход (' . Yii::$app->user->identity->username . ')',
                                    ['class' => 'btn btn-link logout']
                                )
                                . Html::endForm()
                                . '</li>';
                        }
                        echo StoryNav::widget([
                            'items' => $menuItems,
                            'id' => false,
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-large header">
            <div class="row">
                <div class="col-md-5 col-sm-4 col-xs-4">
                    <?php
                    $menuItems = [
                        ['label' => 'Главная', 'url' => ['/site/index']],
                        ['label' => 'Контакты', 'url' => ['/site/contact']],
                        ['label' => 'Истории', 'url' => ['/story/index']],
                        ['label' => 'Подписки', 'url' => ['/site/pricing']],
                    ];
                    echo StoryNav::widget([
                        'options' => ['class' => 'menu'],
                        'items' => $menuItems,
                    ]);
                    ?>
                    <button type="button" class="menu-button">
                        <span></span>
                    </button>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-3 text-center">
                    <div class="logo"><?= Html::a(Html::img('/images/logo.png', ['alt' => 'logo']), ['/site/index']) ?></div>
                </div>
                <div class="col-md-5 col-sm-5 col-xs-5 text-right">
                    <ul class="info-header">
                        <li><a href="#"><i class="fas fa-phone-volume"></i>+91-141-4007601</a></li>
                        <li class="search-icon"><a href="#"><i class="fas fa-search"></i>search</a></li>
                    </ul>
                    <ul class="social-icon">
                        <li class="facebook"><a href="#"><i class="fab fa-facebook"></i></a></li>
                        <li class="google"><a href="#"><i class="fab fa-google-plus"></i></a></li>
                        <li class="tumblr"><a href="#"><i class="fab fa-tumblr"></i></a></li>
                        <li class="instagram"><a href="#"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
            <form class="search" style="display: none">
                <input type="text" placeholder="Search...">
                <span class="close"><img src="/images/close2.png" alt="close"></span>
            </form>
        </div>
    </header>
    <?php
    $controller = Yii::$app->controller;
    $default_controller = Yii::$app->defaultRoute;
    $isHomePage = (($controller->id === $default_controller) && ($controller->action->id === $controller->defaultAction));
    ?>
    <?php if (!$isHomePage): ?>
    <div class="breadcrumb-top <?= ($isStoryViewPage ? '' : 'bg-yellow') ?>">
        <div class="container">
            <h2 <?= ($isStoryViewPage ? 'class="title"' : '') ?>>H2</h2>
        <?= Breadcrumbs::widget([
            'tag' => 'ol',
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        </div>
    </div>
    <?php endif ?>
    <?= $content ?>
    <footer class="bg-yellow">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="subscribe text-center">
                        <h2>Join our secret society</h2>
                        <form>
                            <div class="form-group">
                                <input type="text" placeholder="Enter your email...">
                                <div class="custom-btn bg-black text-yellow">enter</div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget-page">
                        <h4 class="widget-title">Customer Care</h4>
                        <a href="404.html">Register</a>
                        <a href="404.html">My Account</a>
                        <a href="404.html">Track Order</a>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget-page">
                        <h4 class="widget-title">FAQ</h4>
                        <a href="404.html">Ordering Info</a>
                        <a href="404.html">Shipping &amp; Delivery</a>
                        <a href="404.html">Our Guarantee</a>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget-page">
                        <h4 class="widget-title">Our company</h4>
                        <a href="404.html">About</a>
                        <a href="blog.html">Press</a>
                        <a href="single-product.html">Products</a>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget-contact">
                        <h4 class="widget-title">contact usy</h4>
                        <address>123 6th St. Melbourne, FL 32904<br>Phone: (125) 546-4478<br>Email: yesorganic.com</address>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>Copyright &copy; 2018</p>
            </div>
        </div>
        <div id="back-to-top"><i class="fa fa-angle-up"></i></div>
    </footer>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
