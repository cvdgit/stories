<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $userToken common\models\UserToken */
/* @var $task common\models\StudyTask */
$link = $userToken->getLoginUrl($task);
?>
<div class="token-login-reset">
    <p>Уважаемый(ая) <?= Html::encode($user->getProfileName()) ?>.</p>

    <p>Вам назначен курс "<?= Html::encode($task->title) ?>".</p>

    <p>Вы можете пройти этот курс с портала Wikids, зайдя по ссылке <?= Html::a(Html::encode($link), $link) ?></p>
</div>