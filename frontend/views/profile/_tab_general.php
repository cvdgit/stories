<?php

/** @var $model common\models\User */
/** @var $activePayment common\models\Payment */

use yii\helpers\Html;

?>
<div class="profile-tab-content payment-tab">
    <p><strong>Пользователь:</strong> <?= Html::encode($model->username) ?></p>
    <p><strong>Подписка:</strong>
    <?php if ($activePayment === null): ?>
    приобретите <?= Html::a('подписку', ['/pricing']) ?> для просмотра всех историй
    <?php else: ?>
    <?= $activePayment->rate->title ?> с <?= Yii::$app->formatter->asDate($activePayment->payment) ?> по <?= Yii::$app->formatter->asDate($activePayment->finish) ?>
    <?php endif ?>
    </p>
    <p><strong>Email:</strong> <?= Html::encode($model->email) ?></p>
    <p class="text-center" style="margin: 40px 0">
        <?= Html::a('Редактировать профиль', ['/profile/update'], ['class' => 'btn']) ?>
    </p>
</div>
