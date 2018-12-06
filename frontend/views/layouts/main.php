<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\Menu;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
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
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                        <?php
                        $menuItems = [];
                        if (Yii::$app->user->isGuest) {
                            $menuItems[] = ['label' => 'Вход', 'url' => ['/site/login']];
                            $menuItems[] = ['label' => 'Регистрация', 'url' => ['/site/signup']];
                        } else {
                            $menuItems[] = ['label' => 'Профиль', 'url' => ['/profile/index']];
                            $menuItems[] = ['label' => Html::beginForm(['/site/logout'], 'post')
                                . Html::submitButton('Выход (' . Yii::$app->user->identity->username . ')', ['class' => 'cst-btn-a'])
                                . Html::endForm() ];
                        }
                        echo Menu::widget([
                            'encodeLabels' => false,
                            'items' => $menuItems,
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
                        ['label' => 'Истории', 'url' => ['/story/index']],
                        ['label' => 'Подписки', 'url' => ['/rate/index']],
                        ['label' => 'Контакты', 'url' => ['/site/contact']],
                    ];
                    echo Menu::widget([
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
                        <li><a href="#"><i class="fas fa-phone-volume"></i>+7(499)703-35-25</a></li>
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
    <?php if (isset($this->params['breadcrumbs']) && sizeof($this->params['breadcrumbs']) > 0): ?>
    <div class="breadcrumb-top <?= ($isStoryViewPage ? '' : 'bg-yellow') ?>">
        <div class="container">
            <h2 <?= ($isStoryViewPage ? 'class="title"' : '') ?>><?= $this->title ?></h2>
            <?= Breadcrumbs::widget([
                'tag' => 'ol',
                'links' => $this->params['breadcrumbs'],
            ]) ?>
        </div>
    </div>
    <?php endif ?>
    <!-- < ?= Alert::widget() ?> -->
    <?= $content ?>
    <footer class="bg-yellow">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-sm-6 col-xs-12">
                    <div class="widget-contact">
                        <h4 class="widget-title">Wikids</h4>
                        <address>Телефон: (125) 546-4478</address>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-sm-6 col-xs-12">
                    <div class="widget-contact">
                        <address>Email: yesorganic.com</address>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget-contact cst-copyright">
                        <span>Copyright &copy; 2018</span>
                    </div>
                </div>
            </div>
        </div>
        <div id="back-to-top"><i class="fa fa-angle-up"></i></div>
    </footer>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
