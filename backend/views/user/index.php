<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\UserHelper;
use backend\widgets\grid\RoleColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        'id',
        'username',
        'email:email',
        'created_at:datetime',
        [
            'attribute' => 'status',
            'value' => static function($model) {
                return UserHelper::getStatusText($model->status);
            },
            'filter' => UserHelper::getStatusArray(),
        ],
        [
            'attribute' => 'active_payment',
            'value' => 'activePayment.rate.title',
        ],
        'auth.source',
        [
            'attribute' => 'role',
            'label' => 'Роль',
            'class' => RoleColumn::class,
        ],
        [
        	'class' => ActionColumn::class,
        	'template' => '{update} {delete}',
        ],
    ],
]) ?>
