<?php

use yii\helpers\Html;

/* @var $user common\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['/signup/signup-confirm', 'token' => $user->email_confirm_token]);
?>
<div class="password-reset">
    <p>Привет <?= Html::encode($user->username) ?>,</p>
    <p>Следуйте приведенной ниже ссылке, чтобы подтвердить свой адрес электронной почты:</p>
    <p><?= Html::a(Html::encode($confirmLink), $confirmLink) ?></p>
</div>
