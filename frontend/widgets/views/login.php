<?php

use common\models\LoginForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
/**
 * @var LoginForm $model
 * @var View $this
 */
?>
<div class="modal fade site-dialog" id="wikids-login-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <h4 class="modal-title">Вход<br><span>в личный кабинет</span></h4>
      </div>
      <div class="modal-body">
        <?php
        $form = ActiveForm::begin([
          'action' => ['/auth/login'],
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
        echo $form->field($model, 'email', [
          'inputOptions' => ['autofocus' => true, 'autocomplete' => 'off', 'placeholder' => 'Логин'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-email"></span>'],
          'template' => $fieldTemplate,
        ])->label(false);
        echo $form->field($model, 'password', [
          'inputOptions' => ['placeholder' => 'Пароль', 'autocomplete' => 'off'],
          'parts' => ['{icon}' => '<span class="input-group-addon icon icon-password"></span>'],
          'template' => $fieldTemplate,
        ])->passwordInput()->label(false);
        echo Html::submitButton('Войти', ['class' => 'btn button', 'style' => 'margin-top: 20px', 'data-loading-text' => 'Вход...']);
        ActiveForm::end();
        ?>
        <div class="password-request">
          <?= Html::a('Забыли пароль?', ['/site/request-password-reset']) ?>
        </div>
        <div class="social-signup">
          Авторизуйтесь через соцсети:
            <?= yii\authclient\widgets\AuthChoice::widget([
                'options' => ['class' => 'social-network'],
                'baseAuthUrl' => ['/auth/auth'],
                'popupMode' => true,
            ]) ?>
        </div>
      </div>
      <div class="modal-footer">
        <div class="modal-footer-inner">
          <div>Еще не зарегистрированы?</div>
          <a href="#" data-toggle="modal" data-dismiss="modal" data-target="#wikids-signup-modal">Зарегистрироваться</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {

    function loginOnBeforeSubmit(e) {
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
            dataType: 'json'
        })
        .then(function(response) {
            if (response && response.success) {
              if (response.returnUrl) {
                document.location.replace(response.returnUrl);
              }
              else {
                  document.location.reload();
              }
            }
            else {
                toastr.warning((response && response.message) || 'Произошла неизвестная ошибка');
            }
        })
        .fail(function(response) {
          toastr.error((response && response.message) || 'Произошла неизвестная ошибка');
        })
        .always(function() {
          submitButton.button('reset');
        });
        return false;
    }
    $('#login-form')
      .on('beforeSubmit', loginOnBeforeSubmit)
      .on('submit', function(e) {
          e.preventDefault();
      });
})();
JS
);
