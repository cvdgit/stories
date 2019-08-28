<?php

/* @var $this yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use common\rbac\UserRoles;
use common\widgets\ToastrFlash;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\widgets\Alert;
use yii\helpers\Html;

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
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Перейти к сайту',
        'brandUrl' => '/',
        'innerContainerOptions' => ['class' => 'container-fluid'],
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    NavBar::end();
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <?php if (isset($this->params['sidebarMenuItems']) && count($this->params['sidebarMenuItems']) > 0): ?>
                <?= Nav::widget([
                    'options' => ['class' => 'nav-sidebar'],
                    'items' => $this->params['sidebarMenuItems'],
                ]) ?>
                <?php endif ?>
                <?php if (Yii::$app->user->can(UserRoles::PERMISSION_ADMIN_PANEL)): ?>
                <?= Nav::widget([
                    'options' => ['class' => 'nav-sidebar'],
                    'items' => [
                        ['label' => 'Главная', 'url' => ['/site/index']],
                        ['label' => 'Истории', 'url' => ['/story/index']],
                        ['label' => 'Категории', 'url' => ['/category/index'], 'active' => Yii::$app->controller->id === 'category'],
                        ['label' => 'Пользователи', 'url' => ['/user/index']],
                        ['label' => 'Опечатки', 'url' => ['/feedback/index']],
                        ['label' => 'Комментарии', 'url' => ['/comment/index']],
                        ['label' => 'Подписки', 'url' => ['/rate/index']],
                        ['label' => 'Блог', 'url' => ['/news/admin', 'status' => \common\models\News::STATUS_PROPOSED], 'active' => Yii::$app->controller->id === 'news'],
                        ['label' => 'Тесты', 'url' => ['/test/index'], 'active' => Yii::$app->controller->id === 'test'],
                    ],
                ]) ?>
                <?php endif ?>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
<?= ToastrFlash::widget() ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>