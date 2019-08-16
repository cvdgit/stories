<?php

use yii\grid\GridView;

GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'title',
        'description',
        'status',
        'created_at:datetime',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]);