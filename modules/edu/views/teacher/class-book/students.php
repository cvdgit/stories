<?php

declare(strict_types=1);

use modules\edu\forms\teacher\ClassBookForm;
use modules\edu\widgets\TeacherMenuWidget;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var ClassBookForm $formModel
 */

$this->title = 'Редактирование класса';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1><?= Html::encode($formModel->name) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            'name',
            [
                'attribute' => 'studentLogin.username',
                'label' => 'Логин',
            ],
            [
                'attribute' => 'studentLogin.password',
                'label' => 'Пароль',
            ],
        ],
    ]) ?>

    <div style="margin-bottom: 40px">
        <?= Html::a('Добавить ученика', ['/edu/teacher/class-book/create-student', 'id' => $formModel->getId()], ['class' => 'btn btn-small']) ?>
    </div>
</div>
