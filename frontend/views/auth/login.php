<?php

declare(strict_types=1);

use common\models\LoginForm;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $model LoginForm
 * @var string $storyInfo
 */

$title = 'Вход';
$this->setMetaTags($title, $title, 'wikids, сказки, истории', $title);
?>
<div class="container" style="padding-top:3rem">

    <?php if ($storyInfo !== ''): ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="alert alert-info text-center" role="alert" style="line-height: 1.5">
                Что бы получить доступ к истории <strong><?= $storyInfo ?></strong> необходимо авторизоваться.
            </div>
        </div>
    </div>
    <?php endif ?>

    <div class="auth-wrap text-center">
        <div class="auth-header">
            <h1 class="auth-header__text">Вход в Wikids</h1>
        </div>
        <div class="auth-form-wrap">
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'story-form']]); ?>
            <div class="auth-form-row">
                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Логин']) ?>
            </div>
            <div class="auth-form-row">
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль']) ?>
            </div>
            <div class="auth-form-row">
                <div class="checkbox">
                    <?= Html::activeCheckbox($model, 'rememberMe', ['style' => 'margin-top: 1px']); ?>
                </div>
            </div>
            <div class="auth-form-row">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-block auth-form-submit']) ?>
                <?= Html::activeHiddenInput($model, 'returnUrl'); ?>
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
                <a href="<?= Url::to(['/signup/request']) ?>">Зарегистрироваться</a>
            </div>
            <div class="auth-links__divider">|</div>
            <div>
                <?= Html::a('Восстановить пароль', ['/site/request-password-reset']) ?>
            </div>
        </div>
    </div>
</div>
