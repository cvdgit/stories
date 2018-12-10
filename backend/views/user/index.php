<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'username',
        'email:email',
        'created_at:datetime',
        'updated_at:datetime',
        [
            'attribute' => 'status',
            'value' => function($model) {
                return $model->getStatusText();
            },
            'filter' => User::getStatusArray(),
        ],
        [
            'attribute' => 'active_payment',
            'value' => 'activePayment.rate.title',
        ],
        [
        	'class' => 'yii\grid\ActionColumn',
        	'template' => '{update} {delete}',
        ],
    ],
]) ?>
