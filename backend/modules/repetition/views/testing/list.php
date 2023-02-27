<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var int $testId
 */
?>
<div id="repetition-wrap">
    <?php Pjax::begin(['id' => 'pjax-repetition']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            'studentName:text:Ученик',
            'scheduleName:text:Расписание',
            [
                'attribute' => 'lastItem',
                'label' => 'Дата последнего повторения',
                'value' => static function ($model) {
                    return SmartDate::dateSmart($model['lastItem'], true);
                },
            ],
            [
                'label' => 'Пройдено',
                'value' => static function ($model) {
                    return $model['repetitionItemsCount'] . ' из ' . $model['scheduleItemsCount'];
                },
            ],
            [
                'format' => 'raw',
                'value' => static function ($model) use ($testId) {
                    return '<a class="delete-item" href="' . Url::to(['/repetition/testing/delete', 'test_id' => $testId, 'student_id' => $model['studentId'], 'schedule_id' => $model['scheduleId']]) . '"><i class="glyphicon glyphicon-trash"></i></a>';
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php
$url = Url::to(['/repetition/testing/list', 'test_id' => $testId]);
$this->registerJs(<<<JS
(function() {
    $('#repetition-wrap').on('click', '.delete-item', function(e) {
        e.preventDefault();

        if (!confirm('Подтверждаете?')) {
            return;
        }

        $.getJSON($(this).attr('href'))
            .done(response =>  {
                if (response && response.success) {
                    $.pjax.reload('#pjax-repetition', {
                        url: '$url',
                        replace: false,
                        timeout: 3000
                    });
                }
            });
    });
})();
JS
);
