<?php

use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\edu\models\EduTopicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Темы';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <?= AdminHeaderWidget::widget([
        'title' => Html::encode($this->title),
        'content' => Html::a('Создать тему', ['create'], ['class' => 'btn btn-default btn-sm btn-outline-secondary']),
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'classProgram.eduPath',
                'label' => 'Программа обучения',
            ],
            'name',
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
