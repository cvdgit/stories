<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use common\helpers\SubscriptionHelper;

/** @var $model common\models\SubscriptionForm */

?>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'activate-subscription-form',
    ]
]); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Активировать подписку</h4>
</div>
<div class="modal-body">
<?= $form->field($model, 'subscription_id')->dropDownList(SubscriptionHelper::getSubscriptionArray(), ['prompt' => 'Выбрать']) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Активировать подписку', ['class' => 'btn btn-primary']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
</div>
<?php ActiveForm::end(); ?>

<?php
$js = <<< JS
function subscriptionOnBeforeSubmit(e)
{
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: new FormData(form[0]),
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
            $('#modal-subscription').modal('hide');
        },
        error: function(data) {
            toastr.warning('', data);
        }
    });
    return false;
}
$('#activate-subscription-form')
  .on('beforeSubmit', subscriptionOnBeforeSubmit)
  .on('submit', function(e) {
      e.preventDefault();
  });
JS;
$this->registerJs($js, View::POS_READY);
?>