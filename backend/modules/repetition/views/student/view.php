<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var int $studentId
 */

$this->title = 'Повторения';

$this->registerJs($this->renderFile('@backendModules/repetition/views/student/_view.js'));
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2">
        <a href="<?= Url::to(['/repetition/student/list']); ?>"><i class="glyphicon glyphicon-arrow-left back-arrow"></i></a>
        <?= $this->title ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <a id="create-repetition" href="<?= Url::to(['/repetition/student/create-repetition', 'id' => $studentId]); ?>" class="btn btn-primary">Создать повторения</a>
        </div>
    </div>
</div>

<div id="repetition-wrap">
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
</div>
