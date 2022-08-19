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

$this->registerCss(<<<CSS
.header-block {
    display: flex;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    margin-top: 20px;
}
CSS
);
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/teacher/class-book/index']) ?> <?= Html::encode($this->title) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <?= Html::a('Добавить ученика', ['/edu/teacher/class-book/create-student', 'id' => $formModel->getId()], ['class' => 'btn btn-small']) ?>
            </div>
        </div>
    </div>

    <div>
        <p class="lead">Класс: <?= Html::encode($formModel->name) ?></p>
    </div>

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
</div>
