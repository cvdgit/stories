<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model frontend\models\ContactRequestForm */
?>
<?php $form = ActiveForm::begin([
    'action' => ['contact/create'],
    'options' => [
        'id' => 'contact-request-form-bottom',
        'class' => 'contact-form',
    ],
]) ?>
    <div class="contact-form__controls">
        <?= $form->field($model, 'name')->textInput(['placeholder' => 'Ваше имя', 'autocomplete' => 'off'])->label(false) ?>
        <?= $form->field($model, 'phone')->textInput(['placeholder' => '+7 (999) 999-99-99', 'autocomplete' => 'off'])->label(false) ?>
        <?= $form->field($model, 'text')->textarea(['rows' => 6, 'placeholder' => 'Введите ваш вопрос'])->label(false) ?>
    </div>
    <div class="contact-form-submit__wrap">
        <button type="submit" class="button">Отправить</button>
    </div>
    <div class="discount-agree">
        <p class="discount-agree__text">Нажимая на кнопку вы принимаете<br><?= Html::a('пользовательское соглашение', ['site/policy'], ['class' => 'discount-agree__link']) ?></p>
    </div>
<?php ActiveForm::end() ?>
<?php
$this->registerJs(<<<JS
(function() {
    
    function btnLoading(elem) {
        $(elem).attr("data-original-text", $(elem).html());
        $(elem).prop("disabled", true);
        $(elem).html('<i class="spinner-border"></i> Загрузка...');
    }

    function btnReset(elem) {
        $(elem).prop("disabled", false);
        $(elem).html($(elem).attr("data-original-text"));
    }
    
    function setRequest(text) {
        $('#contact-request-block').html('<div class="request-info"><p class="request-info__text">' + text + '</p></div>');
    }
    
    var doneCallback = function(response) {
        if (response && response.success) {
            setRequest(response['message'] || 'Успешно');
        }
        else {
            setRequest(response['message'] || 'Неизвестная ошибка');
        }
    };
    var failCallback = function(response) {
        setRequest(response.responseJSON.message);
    }
    var alwaysCallback = function() {

    }
    
    $('#contact-request-form-bottom')
        .on('beforeSubmit', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type=submit]');
            btnLoading(btn);
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: new FormData(this),
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false
            })
                .done(doneCallback)
                .fail(failCallback)
                .always(function() {
                    btnReset(btn);
                    if (typeof alwaysCallback === 'function') {
                        alwaysCallback();
                    }
                });
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS
);