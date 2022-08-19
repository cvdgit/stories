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
        <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2"><?= Html::encode($this->title) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <?= Html::a('Добавить класс', ['/edu/teacher/class-book/create'], ['class' => 'btn btn-small']) ?>
            </div>
        </div>
    </div>

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
</div>
