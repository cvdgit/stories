<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model backend\models\StoryBatchCommandForm */

$form = ActiveForm::begin([
    'action' => ['/story/batch'],
]);
echo $form->field($model, 'command')->dropDownList(['AccessBySubscription' => 'По подписке', 'AccessFree' => 'Бесплатно'], ['prompt' => 'Выберите команду']);
echo $form->field($model, 'story_ids')->hiddenInput()->label(false);
echo Html::submitButton('Выполнить', ['class' => 'btn btn-primary']);
ActiveForm::end();

$inputID = Html::getInputId($model, 'story_ids');
$js = <<< JS
function batchActionOnBeforeSubmit() {
    
    let rows = $('#w1').yiiGridView('getSelectedRows'),
        form = $(this);
    
    $('#$inputID', form).val(rows.join(','));
    
    let button = $('button[type=submit]', form);
    button.button('loading');
    
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: form.serialize(),
        success: function(data) {
            if (data.success) {
                $.pjax.reload({container: "#pjax-stories"});
            }
        }
    }).always(function() {
        button.button('reset');
    });
}
$('#{$form->getId()}')
  .on('beforeSubmit', batchActionOnBeforeSubmit)
  .on('submit', function(e) {
    e.preventDefault();
  });
JS;
$this->registerJs($js);
