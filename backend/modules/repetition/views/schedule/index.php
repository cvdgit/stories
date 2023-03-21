<?php

declare(strict_types=1);

use backend\modules\repetition\Schedule\ScheduleSearch;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ScheduleSearch $searchModel
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Расписание повторения';
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2"><?= $this->title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <a href="<?= Url::to(['/repetition/student/list']); ?>" class="btn btn-primary">Ученики</a>
            <a href="<?= Url::to(['/repetition/schedule/create']); ?>" class="btn btn-primary">Создать расписание</a>
        </div>
    </div>
</div>

<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Расписание',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
