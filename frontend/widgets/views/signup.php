<?php
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
/** @var $model frontend\models\SignupForm */
?>
<div class="modal fade site-dialog" id="wikids-signup-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Регистрация<br><span>в Wikids</span></h4>
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
        $fieldTemplate = "<div class='input-wrapper'>\n{icon}\n{input}\n</div>\n{hint}\n{error}"; ?>
        <?= $form->field($model, 'email', [
          'inputOptions' => ['placeholder' => 'Email'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-email"></span>'],
          'template' => $fieldTemplate,
        ])->label(false) ?>
        <?= $form->field($model, 'password', [
          'inputOptions' => ['placeholder' => 'Пароль'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-password"></span>'],
          'template' => $fieldTemplate,
        ])->passwordInput()->label(false) ?>
        <div class="signup-agreement">
              <?= $form->field($model, 'agree', [
                  'parts' => ['{hint}' => Html::a('пользовательское соглашение', ['/policy'], ['target' => '_blank'])],
                  'checkboxTemplate' => "<div class=\"checkbox\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{hint}\n</div>",
              ])->checkbox() ?>
        </div>
        <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn button', 'style' => 'margin:20px 0', 'data-loading-text' => 'Регистрация...']) ?>
        <?php ActiveForm::end(); ?>
        <div class="social-signup">
            <p>Авторизуйтесь через соцсети:</p>
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
          <a href="#" data-toggle="modal" data-dismiss="modal" data-target="#wikids-login-modal">Войти</a>
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
$this->registerJs($js, View::POS_READY);