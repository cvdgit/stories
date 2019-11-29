<?php

use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
Pjax::begin(['id' => 'comment-list-pjax', 'enablePushState' => false]);
echo ListView::widget([
    'layout' => '{items}',
    'dataProvider' => $dataProvider,
    'itemOptions' => ['tag' => false],
    'emptyText' => 'Комментариев пока нет',
    'emptyTextOptions' => ['tag' => 'h4'],
    'itemView' => '_comment',
    'sorter' => false,
    'pager' => false,
]);
Pjax::end();
