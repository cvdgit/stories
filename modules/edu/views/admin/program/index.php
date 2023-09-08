<?php

declare(strict_types=1);

use modules\edu\models\EduProgramSearch;
use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var EduProgramSearch $searchModel
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Предметы';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <?= AdminHeaderWidget::widget([
        'title' => Html::encode($this->title),
        'content' => Html::a('Создать предмет', ['create'], ['class' => 'btn btn-default btn-sm btn-outline-secondary']),
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'columns' => [
            'name',
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
