<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use common\helpers\UserHelper;

/** @var $this yii\web\View */
/** @var $model common\models\User */

$this->title = 'Пользователь: ' . $model->username;
$this->params['sidebarMenuItems'] = [
    ['label' => $model->username, 'url' => ['/user/update', 'id' => $model->id]],
    ['label' => 'Подписка', 'url' => ['/user/subscriptions', 'id' => $model->id]],
];
?>
<div class="user-view">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="box">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'username',
                    'email:email',
                    [
                        'attribute' => 'status',
                        'value' => UserHelper::getStatusText($model->status),
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Role',
                        'value' => implode(', ', ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($model->id), 'description')),
                        'format' => 'raw',
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>
