<?php
use common\helpers\SmartDate;
use yii\helpers\Html;
use yii\grid\GridView;
/** @var $dataProvider yii\data\ActiveDataProvider */
$css = <<<CSS
.study-task-list a {
    text-decoration: underline;
}
CSS;
$this->registerCss($css);
?>
<div class="study-task-list">
    <h3>Назначенные задания</h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'title',
                'label' => 'Задание',
                'format' => 'html',
                'value' => static function($item) {
                    return Html::a($item['title'], ['study/task', 'id' => $item['id']]);
                }
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
            ]
        ],
    ]) ?>
</div>