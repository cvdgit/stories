<?php

use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<div class="modal fade site-dialog" id="wikids-signup-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Регистрация<br><span>в Wikids</span></h4>
      </div>
      <div class="modal-body">
        <?php 
        $form = ActiveForm::begin([
          'action' => ['/signup/request'],
          'enableClientValidation' => true,
          'options' => [
              'id' => 'signup-form',
              'class' => 'story-form',
          ],
          'fieldConfig' => [
            'options' => [
              'class' => 'input-group',
            ],
          ],
        ]);
        $fieldTemplate = "<div class='input-wrapper'>\n{icon}\n{input}\n</div>\n{hint}\n{error}";
        echo $form->field($model, 'username', [
          'inputOptions' => ['placeholder' => 'Имя пользователя'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-username"></span>'],
          'template' => $fieldTemplate,
        ])->label(false);
        echo $form->field($model, 'email', [
          'inputOptions' => ['placeholder' => 'Email'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-email"></span>'],
          'template' => $fieldTemplate,
        ])->label(false);
        echo $form->field($model, 'password', [
          'inputOptions' => ['placeholder' => 'Пароль'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-password"></span>'],
          'template' => $fieldTemplate,
        ])->passwordInput()->label(false);
        echo Html::submitButton('Зарегистрироваться', ['class' => 'btn', 'style' => 'margin-top: 20px', 'data-loading-text' => 'Регистрация...']);
        ActiveForm::end();
        ?>
        <div class="signup-agreement">
          <span class="icon icon-agreement"></span>
          Я принимаю <?= Html::a('пользовательское соглашение', ['/policy'], ['target' => '_blank']) ?>
        </div>
        <div class="social-signup">
          Авторизуйтесь через соцсети:
            <?= AuthChoice::widget([
                'options' => ['class' => 'social-network'],
                'baseAuthUrl' => ['/auth/auth'],
                'popupMode' => true,
            ]) ?>
        </div>
      </div>
      <div class="modal-footer">
        <div class="modal-footer-inner">
          <div>Уже зарегистрированы?</div>
          <a href="#" data-toggle="modal" data-target="#wikids-login-modal">Войти</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$js = <<< JS
function signupOnBeforeSubmit(e)
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
                $('#wikids-signup-modal').modal('hide');
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
$('#signup-form')
  .on('beforeSubmit', signupOnBeforeSubmit)
  .on('submit', function(e) {
      e.preventDefault();
  });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
