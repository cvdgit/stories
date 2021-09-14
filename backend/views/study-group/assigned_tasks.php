<?php
use yii\grid\GridView;
use yii\helpers\Html;
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $groupModel common\models\StudyGroup */
$this->title = 'Задания для группы';
$this->params['breadcrumbs'] = [
    ['label' => $groupModel->name, 'url' => ['study-group/update', 'id' => $groupModel->id]],
    $this->title,
];
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            'id',
            'title',
            'description:ntext',
            'created_at:datetime',
            'status',
            [
                'format' => 'raw',
                'value' => static function($model) use ($groupModel) {
                    return Html::a('<span class="label label-success">Пользователи</span>', ['study-group/task-users', 'group_id' => $groupModel->id, 'task_id' => $model->id]);
                },
            ],
        ],
    ]) ?>
</div>