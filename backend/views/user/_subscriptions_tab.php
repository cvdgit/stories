<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\PaymentHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var $this yii\web\View */
/** @var $model common\models\User */
/** @var $dataProvider yii\data\ActiveDataProvider  */

?>
<div style="margin: 20px 0">
    <?= Html::a('Создать подписку',
        ['/user/create-subscription', 'user_id' => $model->id],
        ['class' => 'btn btn-default', 'data-toggle' => 'modal', 'data-target' => '#modal-subscription']) ?>
</div>
<div class="row">
    <div class="col-xs-12">
    <?php Pjax::begin(['id' => 'pjax-subscriptions']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute'  => 'rate.title',
                'label' => 'Подписка',
            ],
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
                'value' => function( $model) {
                    /** @var $model common\models\Payment */
                    $button = '';
                    if ($model->isNew()) {
                        $button = Html::a('Активировать', 'javascript:void(0)',
                            [
                                'class' => 'btn btn-success btn-xs',
                                'onclick' => "activateSubscription(event, this, {$model->id})",
                                'data-action' => Url::to(['/user/activate-subscription', 'id' => $model->id]),
                            ]);
                    }
                    if ($model->isValid()) {
                        $button = Html::a('Отменить', 'javascript:void(0)',
                            [
                                'class' => 'btn btn-danger btn-xs',
                                'onclick' => "cancelSubscription(event, this, {$model->id})",
                                'data-action' => Url::to(['/user/cancel-subscription', 'id' => $model->id]),
                            ]);
                    }
                    return $button;
                }
            ],
        ],
    ]) ?>
    <?php Pjax::end() ?>
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
function activateSubscription(event, link, id) {
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
                toastr.success('', 'Подписка успешно активирована');
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
$this->registerJs($js, View::POS_HEAD);
?>
