<?php

declare(strict_types=1);

use modules\edu\widgets\grid\ArrowColumn;
use modules\edu\widgets\TeacherMenuWidget;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Мои классы';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            'name',
            [
                'attribute' => 'class.name',
                'label' => 'Класс',
            ],
            [
                'label' => 'Учеников',
                'attribute' => 'studentCount',
            ],
            [
                'class' => ArrowColumn::class,
                'url' => static function($model) {
                    return ['/edu/teacher/class-book/students', 'id' => $model->id];
                },
            ],
        ],
    ]) ?>

    <div style="margin-bottom: 40px">
        <?= Html::a('Добавить класс', ['/edu/teacher/class-book/create'], ['class' => 'btn btn-small']) ?>
    </div>
</div>
