<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователь: ' . $model->username;

$this->params['sidebarMenuItems'] = [
    ['label' => $model->username, 'url' => ['/user/update', 'id' => $model->id]],
    ['label' => 'Подписки', 'url' => ['/user/subscriptions', 'id' => $model->id]],
];
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'payment',
        'finish',
        'state',
        'rate.title',
        'rate.cost',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]) ?>
