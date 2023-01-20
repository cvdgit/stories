<?php

declare(strict_types=1);

use frontend\models\SignupForm;
use yii\authclient\widgets\AuthChoice;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\captcha\Captcha;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $formModel SignupForm
 */

$title = 'Регистрация';
$this->setMetaTags($title, $title, 'wikids, сказки, истории', $title);
?>

<div class="container" style="padding-top:3rem">

    <div class="auth-wrap text-center">
        <div class="auth-header">
            <h1 class="auth-header__text">Регистрация в Wikids</h1>
        </div>

        <div class="auth-form-wrap">
            <?php $form = ActiveForm::begin(['id' => 'signup-form', 'options' => ['class' => 'story-form']]); ?>
            <div class="auth-form-row">
                <?= $form->field($formModel, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Email']) ?>
            </div>
            <div class="auth-form-row">
                <?= $form->field($formModel, 'password', ['inputOptions' => ['placeholder' => 'Пароль']])->passwordInput() ?>
            </div>
            <div class="auth-form-row">
                <?= $form->field($formModel, 'captcha')->widget(Captcha::class, [
                    'captchaAction' => '/signup/captcha',
                ]); ?>
            </div>
            <div class="auth-form-row">
                <div class="checkbox">
                    <?= $form->field($formModel, 'agree')
                        ->error(false)
                        ->checkbox(['style' => 'margin-top: 1px'])
                        ->hint(Html::a('Пользовательское соглашение', ['/policy'], ['target' => '_blank', 'style' => 'text-decoration: underline'])); ?>
                </div>
            </div>
            <div class="auth-form-row">
                <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-block  auth-form-submit']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

        <div>
            <?= AuthChoice::widget([
                'options' => ['class' => 'social-network'],
                'baseAuthUrl' => ['/auth/auth'],
                'popupMode' => true,
            ]) ?>
        </div>

        <div class="auth-links">
            <div>
                <a href="<?= Url::to(['/auth/login']) ?>">Войти</a>
            </div>
            <div class="auth-links__divider">|</div>
            <div>
                <?= Html::a('Восстановить пароль', ['/site/request-password-reset']) ?>
            </div>
        </div>
    </div>
</div>
