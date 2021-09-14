<?php
use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Задания';
?>
<div class="study-task-index">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать задание', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            'id',
            'title',
            'description:ntext',
            ['attribute' => 'createdBy.username', 'label' => 'Создал'],
            //['attribute' => 'updatedBy.username', 'label' => 'Изменил'],
            'created_at:datetime',
            //'updated_at:datetime',
            [
                'attribute' => 'status',
                'value' => static function($model) {
                    return \common\models\study_task\StudyTaskStatus::asText($model->status);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>