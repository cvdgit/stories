<?php

use modules\edu\models\EduClassProgramSearch;
use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var EduClassProgramSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Программы обучения';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <?= AdminHeaderWidget::widget([
        'title' => Html::encode($this->title),
        'content' => Html::a('Создать программу обучения', ['create'], ['class' => 'btn btn-default btn-sm btn-outline-secondary']),
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'class.name',
                'label' => 'Класс',
            ],
            [
                'attribute' => 'program.name',
                'label' => 'Программа',
            ],
            [
                'attribute' => 'topicsCount',
                'label' => 'Кол-во тем',
            ],
            [
                'class' => ActionColumn::class,
            ],
        ],
    ]) ?>
</div>
