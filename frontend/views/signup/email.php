<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\SignupForm */

$title = 'Завершение регистрации';
$this->setMetaTags($title,
    $title,
    '',
    $title);
?>
<div class="container">
    <h1 class="text-center">Завершение регистрации</h1>
    <div class="site-reset-password">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <p class="info-text">Введите email:</p>
                <?php $form = ActiveForm::begin([
                    'id' => 'reset-password-form',
                    'options' => [
                        'class' => 'story-form',
                    ],
                ]); ?>
                <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'Email']])->passwordInput()->label(false) ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
