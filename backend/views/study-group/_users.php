<?php
use common\helpers\UserHelper;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $groupModel common\models\StudyGroup */
/* @var $usersDataProvider yii\data\ActiveDataProvider */
?>
<div class="clearfix" style="margin-bottom:10px">
    <h4 style="line-height:34px">Участники <span class="pull-right"><?= Html::a('Импортировать', '#import-users-from-text-modal', ['class' => 'btn btn-primary', 'data-toggle' => 'modal']) ?></span></h4>
</div>
<div>
    <?= GridView::widget([
        'dataProvider' => $usersDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'email:email',
            [
                'attribute' => 'status',
                'value' => static function($model) {
                    return UserHelper::getStatusText($model->status);
                },
                'filter' => UserHelper::getStatusArray(),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => static function($url, $model) {
                        return (new \backend\widgets\grid\UpdateButton(['user/update', 'id' => $model->id]))();
                    },
                    'delete' => static function($url, $model) use ($groupModel) {
                        return (new \backend\widgets\grid\DeleteButton(['study-group/delete-user-item', 'group_id' => $groupModel->id, 'user_id' => $model->id]))();
                    }
                ],
            ],
        ],
    ]) ?>
</div>