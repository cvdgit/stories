<?php

use modules\edu\widgets\AdminToolbarWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Управление учениками';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2"><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'columns' => [
            'name',
            'user.email',
            'class.name',
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{delete}',
            ],
        ],
    ]) ?>
</div>
