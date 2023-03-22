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
 */

$this->title = 'Повторения';
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2">
        <a href="<?= Url::to(['/repetition/student/list']); ?>"><i class="glyphicon glyphicon-arrow-left back-arrow"></i></a>
        <?= $this->title ?>
    </h1>
</div>

<div id="repetition-wrap">
    <?php Pjax::begin(['id' => 'pjax-repetition']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            'testName:text:Тест',
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
            'allDone',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
