<?php
use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Группы';
?>
<div class="study-group-index">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать группу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'format' => 'raw',
                'value' => static function($model) {
                    return Html::a('<span class="label label-primary">Задания</span>', ['study-group/assigned-tasks', 'group_id' => $model->id]);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
