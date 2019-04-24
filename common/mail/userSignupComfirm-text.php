<?php

/* @var $user common\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['/signup/signup-confirm', 'token' => $user->email_confirm_token]);
?>
Привет <?= $user->username ?>,

Следуйте приведенной ниже ссылке, чтобы подтвердить свой адрес электронной почты:

<?= $confirmLink ?>