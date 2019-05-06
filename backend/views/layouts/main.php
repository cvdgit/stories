<?php

/* @var $this yii\web\View */
/* @var $content string */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\widgets\Alert;

?>
<?php $this->beginContent('@backend/views/layouts/page.php'); ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Перейти к сайту',
        'brandUrl' => Yii::$app->urlManagerFrontend->createAbsoluteUrl('/site/index'),
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
                <?= Nav::widget([
                    'options' => ['class' => 'nav-sidebar'],
                    'items' => [
                        ['label' => 'Главная', 'url' => ['/site/index']],
                        ['label' => 'Истории', 'url' => ['/story/index']],
                        ['label' => 'Категории', 'url' => ['/category/index']],
                        ['label' => 'Пользователи', 'url' => ['/user/index']],
                        ['label' => 'Опечатки', 'url' => ['/feedback/index']],
                        ['label' => 'Комментарии', 'url' => ['/comment/index']],
                        ['label' => 'Подписки', 'url' => ['/rate/index']],
                    ],
                ]) ?>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>