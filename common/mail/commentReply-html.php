<?php

use yii\helpers\Html;

/** @var $story common\models\Story */
/** @var $commentAuthor common\models\User */
/** @var $replyUser common\models\User */
?>
<div>
    <p>Здравстуйте, <?= $commentAuthor->getProfileName() ?>!</p>
    <p><?= $replyUser->getProfileName() ?> оставил ответ на ваш комментарий к истории <?= $story->title ?></p>
    <p><?= Html::a('Перейти к истории', Yii::$app->urlManager->createAbsoluteUrl(['/story/view', 'alias' => $story->alias])) ?></p>
</div>
