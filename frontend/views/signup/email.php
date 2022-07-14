<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\EmailForm */

$title = 'Завершение регистрации';
$this->setMetaTags($title,
    $title,
    '',
    $title);
?>
<div class="container">
    <h1 class="text-center" style="margin-bottom:40px">Завершение регистрации</h1>
    <h4 class="text-center">Для завершения регистрации необходимо ввести email</h4>
    <div class="site-reset-password">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <p class="info-text">Введите email:</p>
                <?php $form = ActiveForm::begin([
                    'id' => 'save-email-form',
                    'options' => [
                        'class' => 'story-form',
                    ],
                ]); ?>
                <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'Email']])->textInput()->label(false) ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
