<?php

declare(strict_types=1);

use yii\bootstrap\Nav;
use yii\web\View;

/**
 * @var View $this
 */

$this->registerCss(<<<CSS
.admin-toolbar ul li.active a {
    text-decoration: none;
    background-color: #eeeeee;
}
CSS
);
?>

<div class="admin-toolbar clearfix" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px #eee solid">

    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => 'Классы',
                'url' => ['/edu/admin/class/index'],
                'active' => Yii::$app->controller->id === 'admin/class',
            ],
            [
                'label' => 'Предметы',
                'url' => ['/edu/admin/program/index'],
                'active' => Yii::$app->controller->id === 'admin/program',
            ],
            [
                'label' => 'Программы обучения',
                'url' => ['/edu/admin/class-program/index'],
                'active' => Yii::$app->controller->id === 'admin/class-program'
                    || Yii::$app->controller->id === 'admin/topic'
                    || Yii::$app->controller->id === 'admin/lesson',
            ],
/*            [
                'label' => 'Темы',
                'url' => ['/edu/admin/topic/index'],
                'active' => Yii::$app->controller->id === 'admin/topic' || Yii::$app->controller->id === 'admin/lesson',
            ],*/
            [
                'label' => 'Доступ к модулю',
                'url' => ['/edu/admin/user-access/index'],
                'active' => Yii::$app->controller->id === 'admin/user-access',
            ],
        ],
    ]) ?>

</div>
