<?php

declare(strict_types=1);

use yii\web\View;
use yii\widgets\Menu;

/**
 * @var View $this
 * @var string $content
 */
?>
<?php $this->beginContent('@frontend/views/layouts/main.php') ?>
<div class="container">
    <div class="row">
        <div class="col-xs-6 col-xs-offset-3 col-sm-6 col-sm-offset-3 col-md-3 col-md-offset-0 col-lg-3 col-lg-offset-0">
            <!--div-- class="text-center">
                <h3 style="margin-top: 12px">username</h3>
                <div class="cst-box-image text-center">
                    <div class="cst-image-div" id="image-preview" style="background-image: url('/img/no_avatar.png')"></div>
                </div>
            </div-->
            <?= Menu::widget([
                'items' => [
                    [
                        'label' => '<i class="glyphicon glyphicon-cog"></i> Основная информация',
                        'url' => ['/profile'],
                        'active' => Yii::$app->controller->id === 'profile',
                    ],
/*                    [
                        'label' => '<i class="glyphicon glyphicon-user"></i> Ученики',
                        'url' => ['/student/index'],
                        'active' => Yii::$app->controller->id === 'student',
                    ],*/
                    [
                        'label' => '<i class="glyphicon glyphicon-education"></i> История обучения',
                        'url' => ['/training'],
                        'active' => Yii::$app->controller->id === 'training' && Yii::$app->controller->action->id === 'index',
                        'items' => [
                            [
                                'label' => 'История за неделю',
                                'url' => ['/training/week'],
                            ],
                        ],
                    ],
/*                    [
                        'label' => '<i class="glyphicon glyphicon-education"></i> Задания',
                        'url' => ['/study/index'],
                        'active' => Yii::$app->controller->id === 'study',
                        'visible' => Yii::$app->user->can('student'),
                    ],*/
                    /*[
                        'label' => '<i class="glyphicon glyphicon-ruble"></i> Подписки',
                        'url' => ['/payment/index'],
                        'active' => Yii::$app->controller->id === 'payment',
                    ],*/
                ],
                'options' => ['class' => 'profile-menu'],
                'encodeLabels' => false,
                'submenuTemplate' => "\n<ul class='profile-sub-menu'>\n{items}\n</ul>\n",
            ]) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <main class="site-user-profile">
                <?= $content ?>
            </main>
        </div>
    </div>
</div>
<?php $this->endContent() ?>
