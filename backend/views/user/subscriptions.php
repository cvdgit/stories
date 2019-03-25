<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\PaymentHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $subscription backend\models\SubscriptionForm */

$this->title = 'Подписки: ' . $model->username;
$this->params['sidebarMenuItems'] = [
    ['label' => $model->username, 'url' => ['/user/update', 'id' => $model->id]],
    ['label' => 'Подписка', 'url' => ['/user/subscriptions', 'id' => $model->id]],
];
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div id="alert_placeholder"></div>
<p>
    <?= Html::a('Создать подписку', ['/user/activate-subscription', 'user_id' => $model->id], ['class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#modal-subscription']) ?>
</p>
<div class="row">
    <div class="col-xs-12">
    <?php yii\widgets\Pjax::begin(['id' => 'pjax-subscriptions']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'rate.title',
            'payment:datetime',
            'finish:datetime',
            [
                'attribute' => 'state',
                'value' => function($model) {
                    return PaymentHelper::getStatusText($model->state);
                },
                'filter' => PaymentHelper::getStatusArray(),
            ],
            [
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('Отменить', 
                                   'javascript:void(0)',
                                   [
                                    'class' => 'btn btn-danger btn-xs',
                                    'onclick' => "cancelSubscription(event, this, {$model->id})",
                                    'data-action' => \yii\helpers\Url::to(['/user/cancel-subscription', 'id' => $model->id]),
                                   ]);
                }
            ],
        ],
    ]) ?>
    <?php yii\widgets\Pjax::end() ?>
    </div>
</div>

<div class="modal remote fade" id="modal-subscription">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<?php
$js = <<< JS
function cancelSubscription(event, link, id) {
    event.preventDefault();
    $.ajax({
        url: $(link).data('action'),
        type: 'GET',
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
            if (data && data.success) {
                toastr.success('', 'Подписка успешно отменена');
                $.pjax.reload({container: "#pjax-subscriptions"});
            }
            else {
                toastr.warning('', data.error);
            }
        },
        error: function(data) {
            toastr.warning('', data);
        }
    });
    return false;
}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);
?>
