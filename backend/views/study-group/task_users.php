<?php
use yii\grid\GridView;
use common\helpers\SmartDate;
use yii\helpers\Html;
/** @var $dataProvider yii\data\SqlDataProvider */
/** @var $assignedModel common\models\StudyTaskAssign */
$this->title = 'Пользователи';
$this->params['breadcrumbs'] = [
    ['label' => $assignedModel->studyGroup->name, 'url' => ['study-group/update', 'id' => $assignedModel->studyGroup->id]],
    ['label' => $assignedModel->studyTask->title, 'url' => ['study-task/update', 'id' => $assignedModel->studyTask->id]],
    $this->title,
];
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'username',
                'label' => 'Пользователь',
            ],
            [
                'attribute' => 'assign_date',
                'label' => 'Назначено',
                'value' => static function($item) {
                    $assignDate = $item['assign_date'];
                    if ($assignDate === null) {
                        return;
                    }
                    return SmartDate::dateSmart($assignDate, true);
                },
            ],
            [
                'attribute' => 'begin_date',
                'label' => 'Начало',
                'value' => static function($item) {
                    $beginDate = $item['begin_date'];
                    if ($beginDate === null) {
                        return;
                    }
                    return SmartDate::dateSmart($beginDate, true);
                },
            ],
            [
                'attribute' => 'task_status',
                'label' => 'Статус',
                'value' => static function($item) {
                    return \common\models\study_task\StudyTaskProgressStatus::asText((int)$item['task_status']);
                },
            ],
            [
                'attribute' => 'total_time',
                'label' => 'Затраченное время',
            ],
            [
                'attribute' => 'total_mistake',
                'label' => 'Ошибок в тестах',
            ],
        ],
    ]) ?>
</div>