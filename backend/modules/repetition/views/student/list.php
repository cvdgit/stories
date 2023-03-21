<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Ученики с повторением';
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2">
        <a href="<?= Url::to(['/repetition/schedule/index']); ?>"><i class="glyphicon glyphicon-arrow-left back-arrow"></i></a>
        <?= $this->title ?>
    </h1>
</div>

<div id="repetition-wrap">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'studentName',
                'format' => 'raw',
                'label' => 'Ученик',
                'value' => static function($model) {
                    return Html::a($model['studentName'], ['/repetition/student/view', 'id' => $model['studentId']]);
                }
            ],
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
        ],
    ]); ?>
</div>
