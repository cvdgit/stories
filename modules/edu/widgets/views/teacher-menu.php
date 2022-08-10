<?php

declare(strict_types=1);

use yii\bootstrap\Nav;

?>
<div class="teacher-toolbar">
    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                'label' => 'Доска',
                'url' => ['/edu/teacher/default/index'],
                'active' => Yii::$app->controller->id === 'teacher/default'
            ],
            ['label' => 'Задания', 'url' => ['/edu/teacher/home-task/index']],
            ['label' => 'Мои классы', 'url' => ['/edu/teacher/class-book/index']],
        ],
    ]) ?>
</div>
