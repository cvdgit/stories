<?php

declare(strict_types=1);

use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var DataProviderInterface $dataProvider
 * @var View $this
 */

$this->title = 'Классы';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <?= AdminHeaderWidget::widget([
        'title' => Html::encode($this->title),
        'content' => Html::a('Создать класс', ['create'], ['class' => 'btn btn-default btn-sm btn-outline-secondary']),
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
