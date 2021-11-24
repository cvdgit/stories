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
<div id="task-user-list">
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
                'format' => 'raw',
                'value' => static function($item) {
                    $errors = (int) $item['total_mistake'];
                    if ($errors === 0) {
                        return '';
                    }
                    return Html::a(Html::tag('span', $errors, ['class' => 'label label-danger']), '#', [
                        'data-task-id' => $item['task_id'],
                        'data-student-id' => $item['student_id'],
                        'class' => 'test-detail',
                    ]);
                },
            ],
        ],
    ]) ?>
</div>
<div class="modal remote fade" id="test-detail-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<?php
$js = <<<JS
(function() {
    $('#task-user-list').on('click', '.test-detail', function() {
        var taskId = $(this).data('taskId');
        var studentId = $(this).data('studentId');
        $('#test-detail-modal')
            .modal({'remote': '/admin/index.php?r=study-task/test-detail&task_id=' + taskId + '&student_id=' + studentId});
    });
    $('#test-detail-modal').on('hide.bs.modal', function() {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    });
})();
JS;
$this->registerJs($js);