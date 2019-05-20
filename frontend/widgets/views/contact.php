<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

?>
<div class="modal fade site-dialog feedback-dialog" id="wikids-feedback-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Остались <span>вопросы?</span></h4>
        <p>Если у вас есть деловые вопросы или другие вопросы, пожалуйста, заполните следующую форму, чтобы связаться с нами. Спасибо.</p>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin([
          'action' => ['/site/contact'],
          'enableClientValidation' => true,
          'options' => [
              'id' => 'contact-form',
              'class' => 'story-form feedback-form',
          ],
          'fieldConfig' => [
            'options' => [
            ],
          ],
        ]) ?>
          <div class="row">
            <div class="col-md-6">
              <?= $form->field($model, 'name', ['inputOptions' => ['placeholder' => 'Имя пользователя']])->label(false) ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'Email']])->label(false) ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <?= $form->field($model, 'subject', ['inputOptions' => ['placeholder' => 'Тема']])->label(false) ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <?= $form->field($model, 'body', ['inputOptions' => ['placeholder' => 'Сообщение']])->label(false)->textArea(['cols' => 30, 'rows' => 10]) ?>
            </div>
          </div>
          <?= $form->field($model, 'verifyCode')
                   ->label(false)
                   ->widget(Captcha::className(), [
                     'template' => '<div class="row"><div class="col-md-3">{image}</div><div class="col-md-9">{input}</div></div>',
                     'options' => ['class'=> 'form-control', 'placeholder' => 'Код подтверждения', 'autocomplete' => 'off'],
                   ]) ?>
          <div class="row">
            <div class="col-md-6 col-md-offset-3">
              <?= Html::submitButton('Отправить', ['class' => 'btn', 'data-loading-text' => 'Отправка...']) ?>
            </div>
          </div>
        <?php ActiveForm::end() ?>
      </div>
      <div class="modal-footer">
        <div class="modal-footer-inner">
          <div>Или просто позвоните по номеру</div>
          <span class="dialog-phone-number">+7 (499) 703-35-25</span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$js = <<< JS
function contactOnBeforeSubmit(e)
{
    e.preventDefault();
    var form = $(this),
        submitButton = $('button[type=submit]', form);
    submitButton.button('loading');
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: new FormData(form[0]),
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
            if (data) {
              if (data.success) {
                toastr.success('', data.message);
                $('#wikids-feedback-modal').modal('hide');
              }
              else {
                $.each(data.message, function(i, message) {
                  toastr.warning('', message);
                });
              }
            }
            else {
              toastr.warning('', 'Произошла неизвестная ошибка');
            }
        },
        error: function(data) {
          if (data && data.message) {
            toastr.warning('', data.message);
          }
          else {
            toastr.warning('', 'Произошла неизвестная ошибка');
          }
        }
    }).always(function() {
      submitButton.button('reset');
    });
    return false;
}
$('#contact-form')
  .on('beforeSubmit', contactOnBeforeSubmit)
  .on('submit', function(e) {
      e.preventDefault();
  });
$('#wikids-feedback-modal').on('show.bs.modal', function(e) {
  $('#contactform-verifycode-image').click();
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>