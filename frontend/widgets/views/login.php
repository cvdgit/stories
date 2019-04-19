<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<div class="modal fade site-dialog" id="wikids-login-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Войдите<br><span>в личный кабинет</span></h4>
      </div>
      <div class="modal-body">
        <?php 
        $form = ActiveForm::begin([
          'action' => ['/site/login'],
          'enableClientValidation' => true,
          'options' => [
              'id' => 'login-form',
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
        echo $form->field($model, 'password', [
          'inputOptions' => ['placeholder' => 'Пароль'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-password"></span>'],
          'template' => $fieldTemplate,
        ])->passwordInput()->label(false);
        echo Html::submitButton('Войти', ['class' => 'btn', 'style' => 'margin-top: 20px', 'data-loading-text' => 'Вход...']);
        ActiveForm::end();
        ?>
        <div class="password-request">
          <?= Html::a('Забыли логин или пароль?', ['/site/request-password-reset']) ?>
        </div>
        <div class="social-signup">
          Авторизуйтесь через соцсети:
            <?= yii\authclient\widgets\AuthChoice::widget([
                'options' => ['class' => 'social-network'],
                'baseAuthUrl' => ['site/auth'],
                'popupMode' => false,
            ]) ?>
        </div>
      </div>
      <div class="modal-footer">
        <div class="modal-footer-inner">
          <div>Еще не зарегистрированы?</div>
          <a href="#" data-toggle="modal" data-target="#wikids-signup-modal">Зарегистрироваться</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$js = <<< JS
function loginOnBeforeSubmit(e)
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
                document.location.reload();
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
$('#login-form')
  .on('beforeSubmit', loginOnBeforeSubmit)
  .on('submit', function(e) {
      e.preventDefault();
  });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>