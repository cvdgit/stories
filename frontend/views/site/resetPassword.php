<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$title = 'Сброс пароля';
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
?>
<div class="container">
    <h1 class="text-center">Восстановление пароля</h1>
    <div class="site-reset-password">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <p class="info-text">Выберите новый пароль:</p>
                <?php $form = ActiveForm::begin([
                        'id' => 'reset-password-form',
                    'options' => [
                        'class' => 'story-form',
                    ],
                ]); ?>
                <?= $form->field($model, 'password', ['inputOptions' => ['placeholder' => 'Новый пароль']])->passwordInput()->label(false) ?>
                        <?= Html::submitButton('Сохранить', ['class' => 'btn']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
