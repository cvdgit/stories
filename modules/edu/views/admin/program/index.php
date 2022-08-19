<?php

use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $searchModel modules\edu\models\EduProgramSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Предметы';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <?= AdminHeaderWidget::widget([
        'title' => Html::encode($this->title),
        'content' => Html::a('Создать предмет', ['create'], ['class' => 'btn btn-default btn-sm btn-outline-secondary']),
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'columns' => [
            'name',
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
