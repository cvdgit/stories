<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Изображения';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            'source_url',
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'buttons' => [],
                'template' => '{view} {delete}',
            ],
        ],
    ]) ?>
</div>
