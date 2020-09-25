<?php
use yii\grid\GridView;
use yii\helpers\Html;
/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider  */
?>
<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'table-responsive'],
            'columns' => [
                [
                    'format' => 'raw',
                    'attribute' => 'name',
                    'value' => function($model) {
                        return Html::a($model->name, ['history/view', 'id' => $model->id]);
                    }
                ],
                'created_at:datetime',
            ],
        ]) ?>
    </div>
</div>
