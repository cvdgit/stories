<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

$title = 'Запросить сброс пароля';
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
?>
<div class="container">
    <h1 class="text-center">Восстановление пароля</h1>
    <?= Alert::widget() ?>
    <div class="site-request-password-reset">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <p class="info-text">Пожалуйста, заполните свой адрес электронной почты.<br>Будет отправлена ссылка на сброс пароля.</p>
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form',
                    'options' => [
                        'class' => 'story-form',
                    ]
                ]); ?>
                    <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'Email пользователя']])->label(false) ?>
                    <?= Html::submitButton('Отправить', ['class' => 'btn']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
